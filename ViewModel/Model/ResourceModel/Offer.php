<?php
namespace Dnd\OfferManager\Model\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Dnd\OfferManager\Api\Data\OfferInterface;

class Offer extends AbstractDb
{
    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param EntityManager $entityManager
     * @param string $connectionName
     */
    public function __construct(
        protected Context $context,
        private MetadataPool $metadataPool,
        private EntityManager $entityManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * Define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('dnd_offer', 'offer_id');
    }

    /**
     * Get all Categories Ids for an offer
     *
     * @param int $offerId
     * @return array
     */
    public function lookupCategoryIds($offerId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(OfferInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['category' => $this->getTable('dnd_offer_category')], 'category_id')
            ->join(
                ['offer' => $this->getMainTable()],
                'offer.' . $linkField . ' = category.' . $linkField,
                []
            )
            ->where('category.' . $entityMetadata->getIdentifierField() . ' = :offer_id');

        return $connection->fetchCol($select, ['offer_id' => (int)$offerId]);
    }

    /**
     * Save
     *
     * @param AbstractModel $object
     * @return $this|Offer
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * Load
     *
     * @param AbstractModel $object
     * @param string $value
     * @param string $field
     * @return Offer|mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        return $this->entityManager->load($object, $value);
    }

    /**
     * Delete
     *
     * @param AbstractModel $object
     * @return void
     * @throws \Exception
     */
    public function delete(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->entityManager->delete($object);
    }
}
