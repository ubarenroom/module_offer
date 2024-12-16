<?php
namespace Ubarenroom\OfferManager\Test\Unit\ViewModel\Category;

use Ubarenroom\OfferManager\ViewModel\Category\Offer;
use Ubarenroom\OfferManager\Api\OfferRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ViewModelOfferTest extends TestCase
{
    /** @var OfferRepositoryInterface|MockObject */
    private $offerRepository;

    /** @var StoreManagerInterface|MockObject */
    private $storeManager;

    /** @var DateTime|MockObject */
    private $dateTime;

    /** @var TimezoneInterface|MockObject */
    private $timezone;

    /** @var Store|MockObject */
    private $storeMock;

    /** @var Offer */
    private $viewModel;

    protected function setUp(): void
    {
        $this->offerRepository = $this->createMock(OfferRepositoryInterface::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->dateTime = $this->createMock(DateTime::class);
        $this->timezone = $this->createMock(TimezoneInterface::class);
        $this->storeMock = $this->createMock(Store::class);

        $this->viewModel = new Offer(
            $this->offerRepository,
            $this->storeManager,
            $this->dateTime,
            $this->timezone
        );
    }

    public function testGetImageUrlWithImage(): void
    {
        $imagePath = 'offer/image.jpg';
        $mediaBaseUrl = 'https://example.com/media/';

        $this->storeManager->method('getStore')->willReturn($this->storeMock);
        $this->storeMock->expects($this->once())
            ->method('getBaseUrl')
            ->with(
                UrlInterface::URL_TYPE_MEDIA
            )
            ->willReturn($mediaBaseUrl);

        $result = $this->viewModel->getImageUrl($imagePath);

        $this->assertEquals('https://example.com/media/offer/image.jpg', $result);
    }

    public function testGetImageUrlWithoutImage(): void
    {
        $result = $this->viewModel->getImageUrl('');

        $this->assertEquals('', $result);
    }
}
