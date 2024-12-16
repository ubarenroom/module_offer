<?php
namespace Dnd\OfferManager\Model\ResourceModel\Offer\Relation;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Dnd\OfferManager\Model\ResourceModel\Offer;

class ReadHandler implements ExtensionInterface
{
    /**
     * ReadHandler Constructor
     *
     * @param MetadataPool $metadataPool
     * @param Offer $resourceOffer
     */
    public function __construct(
        private MetadataPool $metadataPool,
        private Offer $resourceOffer
    ) {
    }

    /**
     * Execute
     *
     * @param Offer $entity
     * @param array $arguments
     * @return bool|object
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getOfferId()) {
            $categoryIds = $this->resourceOffer->lookupCategoryIds((int)$entity->getOfferId());
            $entity->setData('category_ids', $categoryIds);
        }
        return $entity;
    }
}
