<?php
namespace Ubarenroom\OfferManager\Test\Unit\Model;

use Ubarenroom\OfferManager\Api\Data\OfferInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ubarenroom\OfferManager\Model\Offer
 */
class OfferTest extends TestCase
{
    /**
     * Mock context
     *
     * @var \Magento\Framework\Model\Context|PHPUnit\Framework\MockObject\MockObject
     */
    private $context;

    /**
     * Mock registry
     *
     * @var \Magento\Framework\Registry|PHPUnit\Framework\MockObject\MockObject
     */
    private $registry;

    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Object to test
     *
     * @var \Ubarenroom\OfferManager\Model\Offer
     */
    private $offerModel;

    /**
     * Main set up method
     */
    public function setUp() : void
    {
        $this->objectManager = new ObjectManager($this);
        $this->context = $this->createMock(\Magento\Framework\Model\Context::class);
        $this->registry = $this->createMock(\Magento\Framework\Registry::class);
        $this->offerModel = $this->objectManager->getObject(
            \Ubarenroom\OfferManager\Model\Offer::class,
            [
                'context' => $this->context,
                'registry' => $this->registry,
            ]
        );
    }

    /**
     * Test getIdentities method
     *
     * @return void
     */
    public function testGetIdentities()
    {
        $result = $this->offerModel->getIdentities();
        $this->assertIsArray($result);
    }

    /**
     * Test getOfferId method
     *
     * @return void
     */
    public function testGetOfferId()
    {
        $blockId = 12;
        $this->offerModel->setData(OfferInterface::OFFER_ID, $blockId);
        $expected = $blockId;
        $actual = $this->offerModel->getOfferId();
        self::assertEquals($expected, $actual);
    }

    /**
     * Test setOfferId method
     *
     * @return void
     */
    public function testSetOfferId()
    {
        $blockId = 15;
        $this->offerModel->setOfferId($blockId);
        $expected = $blockId;
        $actual = $this->offerModel->getData(OfferInterface::OFFER_ID);
        self::assertEquals($expected, $actual);
    }

    /**
     * Test getLabel method
     *
     * @return void
     */
    public function testGetLabel()
    {
        $label = 'test030';
        $this->offerModel->setData(OfferInterface::LABEL, $label);
        $expected = $label;
        $actual = $this->offerModel->getLabel();
        self::assertEquals($expected, $actual);
    }

    /**
     * Test Set Label
     *
     * @return void
     */
    public function testSetLabel()
    {
        $label = 'test058';
        $this->offerModel->setLabel($label);
        $expected = $label;
        $actual = $this->offerModel->getData(OfferInterface::LABEL);
        self::assertEquals($expected, $actual);
    }

    /**
     * Test get image
     *
     * @return void
     */
    public function testGetImage()
    {
        $image = 'image_path';
        $this->offerModel->setData(OfferInterface::IMAGE, $image);
        $expected = $image;
        $actual = $this->offerModel->getImage();
        self::assertEquals($expected, $actual);
    }

    /**
     * Test Set Image
     *
     * @return void
     */
    public function testSetImage()
    {
        $image = 'image_test_set';
        $this->offerModel->setImage($image);
        $expected = $image;
        $actual = $this->offerModel->getData(OfferInterface::IMAGE);
        self::assertEquals($expected, $actual);
    }

    /**
     * Test get link
     *
     * @return void
     */
    public function testGetLink()
    {
        $link = 'link085';
        $this->offerModel->setData(OfferInterface::LINK, $link);
        $expected = $link;
        $actual = $this->offerModel->getLink();
        self::assertEquals($expected, $actual);
    }

    /**
     * Test set link
     *
     * @return void
     */
    public function testSetLink()
    {
        $link = 'link085';
        $this->offerModel->setData(OfferInterface::LINK, $link);
        $expected = $link;
        $actual = $this->offerModel->getLink();
        self::assertEquals($expected, $actual);
    }

    /**
     * Test Get Start date
     *
     * @return void
     */
    public function testGetStartDate()
    {
        $date = '2024-02-01';
        $this->offerModel->setData(OfferInterface::START_DATE, $date);
        $expected = $date;
        $actual = $this->offerModel->getStartDate();
        self::assertEquals($expected, $actual);
    }

    /**
     * Test set start date
     *
     * @return void
     */
    public function testSetStartDate()
    {
        $date = '2024-10-12';
        $this->offerModel->setStartDate($date);
        $expected = $date;
        $actual = $this->offerModel->getData(OfferInterface::START_DATE);
        self::assertEquals($expected, $actual);
    }

    /**
     * Test get end date
     *
     * @return void
     */
    public function testGetEndDate()
    {
        $date = '2024-02-08';
        $this->offerModel->setData(OfferInterface::END_DATE, $date);
        $expected = $date;
        $actual = $this->offerModel->getEndDate();
        self::assertEquals($expected, $actual);
    }

    /**
     * Test Set end date
     *
     * @return void
     */
    public function testSetEndDate()
    {
        $date = '2021-02-03';
        $this->offerModel->setEndDate($date);
        $expected = $date;
        $actual = $this->offerModel->getData(OfferInterface::END_DATE);
        self::assertEquals($expected, $actual);
    }
}
