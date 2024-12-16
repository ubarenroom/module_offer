<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ubarenroom\OfferManager\Model\Offer;

use Ubarenroom\OfferManager\Model\ResourceModel\Offer\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Store\Model\StoreManagerInterface;
use Ubarenroom\OfferManager\Model\ImageUploader;
use Magento\Catalog\Model\Category\FileInfo;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;

class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $offerCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManagerInterface $storeManager
     * @param FileInfo $fileInfo
     * @param DirectoryList $directoryList
     * @param File $file
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        protected $name,
        protected $primaryFieldName,
        protected $requestFieldName,
        protected CollectionFactory $offerCollectionFactory,
        protected DataPersistorInterface $dataPersistor,
        protected StoreManagerInterface $storeManager,
        protected FileInfo $fileInfo,
        protected DirectoryList $directoryList,
        protected File $file,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $offerCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Ubarenroom\OfferManager\Model\Offer $offer */
        foreach ($items as $offer) {
            $this->loadedData[$offer->getOfferId()] = $offer->getData();
            $this->manageImage($offer);
        }

        $data = $this->dataPersistor->get('offer');
        if (!empty($data)) {
            $offer = $this->collection->getNewEmptyItem();
            $offer->setData($data);
            $this->loadedData[$offer->getOfferId()] = $offer->getData();
            $this->manageImage($offer);
            $this->dataPersistor->clear('offer');
        }

        return $this->loadedData;
    }

    /**
     * Manage Image data
     *
     * @param \Ubarenroom\OfferManager\Model\Offer $offer
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function manageImage($offer)
    {
        if ($offer->getImage()) {
            $fileInfo = $this->file->getPathInfo($offer->getImage());
            if ($fileInfo) {
                $basename = $fileInfo['basename'];
                $image['image'][0]['name'] = $basename;
                $image['image'][0]['url'] = $this->getMediaUrl($basename);
                $image['image'][0]['size'] = 0;
                $fullData = $this->loadedData;
                $this->loadedData[$offer->getOfferId()] = array_merge($fullData[$offer->getOfferId()], $image);
            }
        }
    }

    /**
     * Get Media Url
     *
     * @param string $path
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl($path = '')
    {
        $mediaUrl = $this->storeManager
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . ImageUploader::BASE_PATH . $path;
        return $mediaUrl;
    }
}
