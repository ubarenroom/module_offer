<?php
namespace Dnd\OfferManager\Test\Unit\Controller\Adminhtml\Offer;

use Dnd\OfferManager\Controller\Adminhtml\Offer\Delete;
use Magento\Backend\App\Action\Context;
use Dnd\OfferManager\Api\OfferRepositoryInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\App\RequestInterface;

class DeleteTest extends TestCase
{
    /** @var Context|MockObject */
    private $context;

    /** @var OfferRepositoryInterface|MockObject */
    private $offerRepository;

    /** @var RedirectFactory|MockObject */
    private $resultRedirectFactory;

    /** @var ManagerInterface|MockObject */
    private $messageManager;

    /** @var RequestInterface|MockObject */
    private $requestManager;

    /** @var Redirect|MockObject */
    private $resultRedirect;

    /** @var Delete */
    private $controller;

    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->offerRepository = $this->createMock(OfferRepositoryInterface::class);
        $this->resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $this->messageManager = $this->createMock(ManagerInterface::class);
        $this->requestManager = $this->createMock(RequestInterface::class);
        $this->resultRedirect = $this->createMock(Redirect::class);
        $this->context->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactory);
        $this->context->method('getMessageManager')->willReturn($this->messageManager);
        $this->context->method('getRequest')->willReturn($this->requestManager);
        $this->resultRedirect->method('setPath')->willReturnSelf();

        $this->controller = new Delete(
            $this->context,
            $this->offerRepository
        );
    }

    private function createRedirect(): void
    {
        $this->resultRedirectFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->resultRedirect);
    }

    private function setPath(string $path, $params = []): void
    {
        $this->resultRedirect->expects($this->once())->method('setPath')->with($path, $params);
    }

    public function testExecuteWithValidOfferId(): void
    {
        $offerId = 1;

        $this->requestManager->expects($this->once())
            ->method('getParam')
            ->with('offer_id')
            ->willReturn($offerId);
        $this->offerRepository->expects($this->once())->method('deleteById')->with($offerId);

        $this->createRedirect();

        $this->messageManager->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('You deleted the offer.'));

        $this->setPath('*/*/');

        $result = $this->controller->execute();

        $this->assertSame($this->resultRedirect, $result);
    }

    public function testExecuteWithInvalidOfferId(): void
    {
        $this->requestManager->expects($this->once())
            ->method('getParam')
            ->with('offer_id')
            ->willReturn(null);
        $this->createRedirect();

        $this->messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('We can\'t find an offer to delete.'));

        $this->setPath('*/*/');

        $result = $this->controller->execute();

        $this->assertSame($this->resultRedirect, $result);
    }

    public function testExecuteWithException(): void
    {
        $offerId = 1;

        $this->requestManager->expects($this->once())
            ->method('getParam')
            ->with('offer_id')
            ->willReturn($offerId);
        $this->offerRepository
            ->method('deleteById')
            ->willThrowException(new \Exception('Error during deletion'));

        $this->createRedirect();

        $this->messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with('Error during deletion');

        $this->setPath('*/*/edit', ['offer_id' => $offerId]);

        $result = $this->controller->execute();

        $this->assertSame($this->resultRedirect, $result);
    }
}
