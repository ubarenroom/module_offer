<?php
namespace Dnd\OfferManager\Test\Unit\Controller\Adminhtml\Offer;

use Dnd\OfferManager\Controller\Adminhtml\Offer\Edit;
use Magento\Backend\App\Action\Context;
use Dnd\OfferManager\Model\Offer;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class EditTest extends TestCase
{
    /** @var Context|MockObject */
    private $context;

    /** @var ManagerInterface|MockObject */
    private $messageManager;

    /** @var RequestInterface|MockObject */
    private $requestManager;

    /** @var PageFactory|MockObject */
    private $resultPageFactory;

    /** @var DataPersistorInterface|MockObject */
    private $dataPersistor;

    /** @var Edit */
    private $controller;

    /** @var Page|MockObject */
    private $resultPage;

    /** @var Offer|MockObject */
    private $offer;

    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->resultPageFactory = $this->createMock(PageFactory::class);
        $this->messageManager = $this->createMock(ManagerInterface::class);
        $this->requestManager = $this->createMock(RequestInterface::class);
        $this->resultPage = $this->createMock(Page::class);
        $this->dataPersistor = $this->createMock(DataPersistorInterface::class);
        $this->offer = $this->createMock(Offer::class);

        $this->context->method('getMessageManager')->willReturn($this->messageManager);
        $this->context->method('getRequest')->willReturn($this->requestManager);
        $this->controller = new Edit(
            $this->context,
            $this->resultPageFactory,
            $this->dataPersistor,
            $this->offer
        );
    }

    /**
     * Test Edit action
     *
     * @param int|null $offerId
     * @return void
     *
     * @dataProvider editActionData
     */
    public function testEditAction(?int $offerId): void
    {
        $this->requestManager->expects($this->once())
            ->method('getParam')
            ->with('offer_id')
            ->willReturn($offerId);
        $offerMock = $this->createMock(\Dnd\OfferManager\Api\Data\OfferInterface::class);

        $this->offer->expects($this->any())
            ->method('load')
            ->with($offerId);
        $this->offer->expects($this->any())
            ->method('getId')
            ->willReturn($offerId);
        $this->offer->expects($this->any())
            ->method('getLabel')
            ->willReturn('New Offer');

        $this->dataPersistor->expects($this->any())
            ->method('set')
            ->with(
                'offer',
                $offerMock
            );

        $resultPageMock = $this->createMock(\Magento\Backend\Model\View\Result\Page::class);

        $this->resultPageFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultPageMock);

        $titleMock = $this->createMock(Title::class);
        $titleMock
            ->method('prepend')
            ->willReturnCallback(function ($arg) {
                if ($arg == __('Add New Offer') || $arg == $this->getTitle()) {
                    return null;
                }
            });
        $pageConfigMock = $this->createMock(Config::class);
        $pageConfigMock->expects($this->once())->method('getTitle')->willReturn($titleMock);

        $resultPageMock->expects($this->once())
            ->method('setActiveMenu')
            ->willReturnSelf();
        $resultPageMock->expects($this->any())
            ->method('addBreadcrumb')
            ->willReturnSelf();

        $resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($pageConfigMock);

        $this->assertSame($resultPageMock, $this->controller->execute());
    }

    public static function editActionData(): array
    {
        return [
            [null, 'New Offer'],
            [2, 'Edit Offer']
        ];
    }
    protected function getTitle()
    {
        return $this->offer->getOfferId() ? $this->offer->getLabel() : __('Add New Offer');
    }
}
