<?php
namespace Dnd\OfferManager\Model\ResourceModel\Offer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define resource model and model
     */
    protected function _construct()
    {
        $this->_init(
            \Dnd\OfferManager\Model\Offer::class,
            \Dnd\OfferManager\Model\ResourceModel\Offer::class
        );
    }
}
