<?php
namespace Ubarenroom\OfferManager\Api\Data;

interface OfferInterface
{

    public const OFFER_ID = 'offer_id';
    public const LINK = 'link';
    public const IMAGE = 'image';
    public const LABEL = 'label';
    public const START_DATE = 'start_date';
    public const END_DATE = 'end_date';
    /**
     * Get offer ID
     *
     * @return int
     */
    public function getOfferId();

    /**
     * Set offer ID
     *
     * @param int $offerId
     * @return $this
     */
    public function setOfferId($offerId);

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * Get image
     *
     * @return string
     */
    public function getImage();

    /**
     * Set image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image);

    /**
     * Get link
     *
     * @return string|null
     */
    public function getLink();

    /**
     * Set link
     *
     * @param string|null $link
     * @return $this
     */
    public function setLink($link);

    /**
     * Get start date
     *
     * @return string|null
     */
    public function getStartDate();

    /**
     * Set start date
     *
     * @param string|null $startDate
     * @return $this
     */
    public function setStartDate($startDate);

    /**
     * Get end date
     *
     * @return string|null
     */
    public function getEndDate();

    /**
     * Set end date
     *
     * @param string|null $endDate
     * @return $this
     */
    public function setEndDate($endDate);
}
