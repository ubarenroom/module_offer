<?php
namespace Dnd\OfferManager\Model\ResourceModel\Offer\Relation;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Dnd\OfferManager\Api\Data\OfferInterface;
use Dnd\OfferManager\Model\ResourceModel\Offer;

class SaveHandler implements ExtensionInterface
{

    /**
     * Save Handler constructor
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
     * Execute function
     *
     * @param Offer $entity
     * @param array $arguments
     * @return bool|object
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(OfferInterface::class);
        $linkField      = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();

        $oldCategories = $this->resourceOffer->lookupCategoryIds((int)$entity->getOfferId());
        $newCategories = (array)$entity->getCategoryIds();

        $table = $this->resourceOffer->getTable('dnd_offer_category');

        $delete = array_diff($oldCategories, $newCategories);
        if ($delete) {
            $where = [
                $linkField . ' = ?'        => $entity->getOfferId(),
                'category_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newCategories, $oldCategories);
        if ($insert) {
            $data = [];
            foreach ($insert as $categoryId) {
                $data[] = [
                    $linkField => $entity->getOfferId(),
                    'category_id' => (int)$categoryId
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}
