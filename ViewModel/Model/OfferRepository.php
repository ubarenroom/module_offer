<?php
namespace Dnd\OfferManager\Model;

use Dnd\OfferManager\Api\OfferRepositoryInterface;
use Dnd\OfferManager\Api\Data\OfferInterface;
use Dnd\OfferManager\Api\Data\OfferSearchResultsInterface;
use Dnd\OfferManager\Api\Data\OfferSearchResultsInterfaceFactory;
use Dnd\OfferManager\Model\ResourceModel\Offer as OfferResource;
use Dnd\OfferManager\Model\ResourceModel\Offer\CollectionFactory as OfferCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class OfferRepository implements OfferRepositoryInterface
{

    /**
     * OfferRepository constructor.
     *
     * @param OfferResource $offerResource
     * @param OfferFactory $offerFactory
     * @param OfferCollectionFactory $offerCollectionFactory
     * @param OfferSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        private OfferResource $offerResource,
        private OfferFactory $offerFactory,
        private OfferCollectionFactory $offerCollectionFactory,
        private OfferSearchResultsInterfaceFactory $searchResultsFactory,
        private CollectionProcessorInterface $collectionProcessor
    ) {
    }

    /**
     * Save
     *
     * @param OfferInterface $offer
     * @return OfferInterface
     * @throws CouldNotSaveException
     */
    public function save(OfferInterface $offer)
    {
        try {
            $this->offerResource->save($offer);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__("Could not save the offer: %1", $e->getMessage()));
        }
        return $offer;
    }

    /**
     * Get By Id
     *
     * @param int $offerId
     * @return OfferInterface|Offer
     * @throws NoSuchEntityException
     */
    public function getById($offerId)
    {
        $offer = $this->offerFactory->create();
        $this->offerResource->load($offer, $offerId);
        if (!$offer->getOfferId()) {
            throw new NoSuchEntityException(__("The offer with ID %1 does not exist.", $offerId));
        }
        return $offer;
    }

    /**
     * Delete
     *
     * @param OfferInterface $offer
     * @return true
     * @throws CouldNotDeleteException
     */
    public function delete(OfferInterface $offer)
    {
        try {
            $this->offerResource->delete($offer);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__("Could not delete the offer: %1", $e->getMessage()));
        }
        return true;
    }

    /**
     * Delete By Id
     *
     * @param int $offerId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($offerId)
    {
        $offer = $this->getById($offerId);
        return $this->delete($offer);
    }

    /**
     * Get List
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return OfferSearchResultsInterface|\Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->offerCollectionFactory->create();

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Get Offers By Category Id
     *
     * @param int $categoryId
     * @param string $currentDate
     * @return OfferInterface[]|\Magento\Framework\DataObject[]
     */
    public function getOffersByCategoryId($categoryId, $currentDate = null)
    {
        $offerCollection = $this->offerCollectionFactory->create();

        if ($currentDate) {
            $offerCollection->getSelect()
                ->where(
                    OfferInterface::START_DATE. ' IS NULL OR ' . OfferInterface::START_DATE .' <= ?',
                    $currentDate
                )->where(
                    OfferInterface::END_DATE . ' IS NULL OR ' . OfferInterface::END_DATE .' >= ?',
                    $currentDate
                );
        }

        $offerCollection->getSelect()->join(
            ['oc' => $this->offerResource->getTable('dnd_offer_category')],
            'main_table.offer_id = oc.offer_id',
            []
        )->where('oc.category_id = ?', $categoryId);

        return $offerCollection->getItems();
    }
}
