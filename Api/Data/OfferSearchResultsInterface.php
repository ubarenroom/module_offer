<?php
namespace Ubarenroom\OfferManager\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OfferSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of offers
     *
     * @return \Ubarenroom\OfferManager\Api\Data\OfferInterface[]
     */
    public function getItems();

    /**
     * Set list of offers
     *
     * @param \Ubarenroom\OfferManager\Api\Data\OfferInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
