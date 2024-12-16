<?php
namespace Dnd\OfferManager\Model;

use Dnd\OfferManager\Api\Data\OfferInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Offer extends AbstractModel implements OfferInterface, IdentityInterface
{
    /**
     * Offer cache tag name
     */
    public const CACHE_TAG = 'offer';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'offer';

    /**
     * Construct
     *
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(\Dnd\OfferManager\Model\ResourceModel\Offer::class);
    }

    /**
     * Get Identities
     *
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getOfferId(), self::CACHE_TAG . '_' . $this->getOfferId()];
    }

    /**
     * Get Offer Id
     *
     * @return int|mixed|null
     */
    public function getOfferId()
    {
        return $this->getData('offer_id');
    }

    /**
     * Set Offer Id
     *
     * @param int $offerId
     * @return Offer
     */
    public function setOfferId($offerId)
    {
        return $this->setData('offer_id', $offerId);
    }

    /**
     * Get Label
     *
     * @return mixed|string|null
     */
    public function getLabel()
    {
        return $this->getData('label');
    }

    /**
     * Set Label
     *
     * @param string $label
     * @return Offer
     */
    public function setLabel($label)
    {
        return $this->setData('label', $label);
    }

    /**
     * Get Image
     *
     * @return mixed|string|null
     */
    public function getImage()
    {
        return $this->getData('image');
    }

    /**
     * Set Image
     *
     * @param string $image
     * @return Offer
     */
    public function setImage($image)
    {
        return $this->setData('image', $image);
    }

    /**
     * Get Link
     *
     * @return mixed|string|null
     */
    public function getLink()
    {
        return $this->getData('link');
    }

    /**
     * Set Link
     *
     * @param string $link
     * @return Offer
     */
    public function setLink($link)
    {
        return $this->setData('link', $link);
    }

    /**
     * Get Start date
     *
     * @return mixed|string|null
     */
    public function getStartDate()
    {
        return $this->getData('start_date');
    }

    /**
     * Set Start date
     *
     * @param string $startDate
     * @return Offer
     */
    public function setStartDate($startDate)
    {
        return $this->setData('start_date', $startDate);
    }

    /**
     * Get End Date
     *
     * @return mixed|string|null
     */
    public function getEndDate()
    {
        return $this->getData('end_date');
    }

    /**
     * Set End Date
     *
     * @param string $endDate
     * @return Offer
     */
    public function setEndDate($endDate)
    {
        return $this->setData('end_date', $endDate);
    }
}
