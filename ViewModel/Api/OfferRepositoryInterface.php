<?php
namespace Dnd\OfferManager\Api;

use Dnd\OfferManager\Api\Data\OfferInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface OfferRepositoryInterface
{
    /**
     * Save an offer
     *
     * @param OfferInterface $offer
     * @return OfferInterface
     */
    public function save(OfferInterface $offer);

    /**
     * Get offer by ID
     *
     * @param int $offerId
     * @return OfferInterface
     */
    public function getById($offerId);

    /**
     * Delete an offer
     *
     * @param OfferInterface $offer
     * @return bool
     */
    public function delete(OfferInterface $offer);

    /**
     * Delete offer by ID
     *
     * @param int $offerId
     * @return bool
     */
    public function deleteById($offerId);

    /**
     * Get list of offers
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Get Offers By Category Id
     *
     * @param int $categoryId
     * @param string $currentDate
     * @return OfferInterface[]
     */
    public function getOffersByCategoryId($categoryId, $currentDate = null);
}
