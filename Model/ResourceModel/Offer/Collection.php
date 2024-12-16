<?php
namespace Ubarenroom\OfferManager\Model\ResourceModel\Offer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define resource model and model
     */
    protected function _construct()
    {
        $this->_init(
            \Ubarenroom\OfferManager\Model\Offer::class,
            \Ubarenroom\OfferManager\Model\ResourceModel\Offer::class
        );
    }
}
