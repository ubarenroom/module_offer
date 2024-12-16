<?php
namespace Dnd\OfferManager\Test\Unit\Controller\Adminhtml\Offer;

use Dnd\OfferManager\Controller\Adminhtml\Offer\Save;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Dnd\OfferManager\Api\OfferRepositoryInterface;
use Dnd\OfferManager\Model\OfferFactory;
use Dnd\OfferManager\Model\Offer;
use Magento\Framework\App\Request\DataPersistorInterface;
use Dnd\OfferManager\Model\ImageUploader;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class SaveTest extends TestCase
{
    /** @var Context|MockObject */
    private $context;

    /** @var ManagerInterface|MockObject */
    private $messageManager;

    /** @var DataPersistorInterface|MockObject */
    private $dataPersistor;

    /** @var OfferRepositoryInterface|MockObject */
    private $offerRepository;

    /** @var OfferFactory|MockObject */
    private $offerFactory;

    /** @var ImageUploader|MockObject */
    private $imageUploader;

    /** @var Date|MockObject */
    private $filterDate;

    /** @var TimezoneInterface|MockObject */
    private $timezone;

    /** @var RequestInterface|MockObject */
    private $requestManager;

    /** @var Redirect|MockObject */
    private $resultRedirect;

    /** @var RedirectFactory|MockObject */
    private $resultRedirectFactory;

    /** @var Save */
    private $controller;

    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->requestManager = $this->createMock(Http::class);
        $this->messageManager = $this->createMock(ManagerInterface::class);
        $this->dataPersistor = $this->createMock(DataPersistorInterface::class);
        $this->offerRepository = $this->createMock(OfferRepositoryInterface::class);
        $this->offerFactory = $this->createMock(OfferFactory::class);
        $this->imageUploader = $this->createMock(ImageUploader::class);
        $this->filterDate = $this->createMock(Date::class);
        $this->timezone = $this->createMock(TimezoneInterface::class);

        $this->resultRedirect = $this->createMock(Redirect::class);
        $this->resultRedirectFactory = $this->createMock(RedirectFactory::class);

        $this->context->method('getRequest')->willReturn($this->requestManager);
        $this->context->method('getMessageManager')->willReturn($this->messageManager);
        $this->context->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactory);

        $this->controller = new Save(
            $this->context,
            $this->dataPersistor,
            $this->offerFactory,
            $this->offerRepository,
            $this->imageUploader,
            $this->filterDate,
            $this->timezone
        );
    }

    public function testExecuteWithValidData(): void
    {
        $offerId = null;
        $postData = [
            'offer_id' => $offerId,
            'name' => 'Test Offer',
            'start_date' => '2024-12-16',
            'category_ids' => [1,2]
        ];

        $this->createRedirect();
        $this->requestManager->expects($this->any())->method('getPostValue')->willReturn($postData);
        $this->requestManager->expects($this->atLeastOnce())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['offer_id', null, $offerId],
                    ['back', null, false],
                ]
            );
        $offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->offerFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($offer);

        $offer->expects($this->once())->method('setData');
        $offer->method('getId')->willReturn($offerId);
        $this->offerRepository->expects($this->once())->method('save')->with($offer);

        $this->dataPersistor->expects($this->any())
            ->method('clear')
            ->with('offer');

        $this->messageManager->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('You saved the offer.'));

        $this->resultRedirect->expects($this->atLeastOnce())->method('setPath')
            ->with('*/*/edit')->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->controller->execute());
    }

    public function testExecuteWithInvalidData(): void
    {
        $this->createRedirect();
        $this->requestManager->expects($this->any())->method('getPostValue')->willReturn(false);
        $this->resultRedirect
            ->expects($this->atLeastOnce())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();
        $this->assertSame($this->resultRedirect, $this->controller->execute());
    }

    public function testExecuteWithSaveException(): void
    {
        $postData = [
            'offer_id' => 1,
            'name' => 'Test Offer',
        ];

        $this->createRedirect();

        $this->requestManager->method('getPostValue')->willReturn($postData);
        $offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->offerFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($offer);

        $this->offerRepository->method('save')->willThrowException(new \Exception('Save error'));

        $this->messageManager->expects($this->once())
            ->method('addExceptionMessage')
            ->with(
                $this->isInstanceOf(\Exception::class),
                __('Something went wrong while saving the offer.')
            );

        $this->resultRedirect
            ->expects($this->atLeastOnce())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->controller->execute());
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
}
