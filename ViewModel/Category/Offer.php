<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Dnd\OfferManager\ViewModel\Category;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Dnd\OfferManager\Api\OfferRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Category image view model
 */
class Offer implements ArgumentInterface
{
    /**
     * @param OfferRepositoryInterface $offerRepository
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        protected OfferRepositoryInterface $offerRepository,
        protected StoreManagerInterface $storeManager,
        protected DateTime $dateTime,
        protected TimezoneInterface $timezone
    ) {
    }

    /**
     * Get Offers about category
     *
     * @param Category $category
     * @return \Dnd\OfferManager\Api\Data\OfferInterface[]
     */
    public function getOffers(Category $category)
    {
        return $this->offerRepository->getOffersByCategoryId($category->getId(), $this->getCurrentDate());
    }

    /**
     * Manage Image Url
     *
     * @param string $image
     * @return string
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl($image): string
    {
        $url = '';
        if ($image) {
            $store = $this->storeManager->getStore();
            $mediaBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            $url = rtrim($mediaBaseUrl, '/') . '/' . $image;
        }
        return $url;
    }

    /**
     * Get Current Date
     *
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCurrentDate()
    {
        $timestamp = $this->timezone->scopeTimeStamp($this->storeManager->getStore());
        $currentDate = $this->dateTime->formatDate($timestamp, false);

        return $currentDate;
    }
}
