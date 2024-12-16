<?php
namespace Dnd\OfferManager\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OfferSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of offers
     *
     * @return \Dnd\OfferManager\Api\Data\OfferInterface[]
     */
    public function getItems();

    /**
     * Set list of offers
     *
     * @param \Dnd\OfferManager\Api\Data\OfferInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
