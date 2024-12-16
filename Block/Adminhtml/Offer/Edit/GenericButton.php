<?php
namespace Dnd\OfferManager\Block\Adminhtml\Offer\Edit;

use Magento\Backend\Block\Widget\Context;
use Dnd\OfferManager\Api\OfferRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{

    /**
     * @param Context $context
     * @param OfferRepositoryInterface $offerRepository
     */
    public function __construct(
        protected Context $context,
        protected OfferRepositoryInterface $offerRepository
    ) {
    }

    /**
     * Return offer ID
     *
     * @return int|null
     */
    public function getOfferId()
    {
        try {
            return $this->offerRepository->getById(
                $this->context->getRequest()->getParam('offer_id')
            )->getOfferId();
        } catch (NoSuchEntityException $e) {
            $this->context->getLogger()->critical($e->getMessage());
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
