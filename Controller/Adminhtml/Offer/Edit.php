<?php
declare(strict_types=1);

namespace Ubarenroom\OfferManager\Controller\Adminhtml\Offer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Ubarenroom\OfferManager\Model\Offer;

/**
 * Controller for the 'offer/edit/index' URL route.
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Admin Resource
     */
    public const ADMIN_RESOURCE = 'Ubarenroom_OfferManager::offer';

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Offer $offer
     */
    public function __construct(
        protected Context $context,
        protected PageFactory $resultPageFactory,
        protected DataPersistorInterface $dataPersistor,
        protected Offer $offer
    ) {
        parent::__construct($context);
    }

    /**
     * Execute Function
     *
     * @return void
     */
    public function execute(): Page
    {
        $offerId = $this->getRequest()->getParam('offer_id');
        $label = __('Add New Offer');
        if ($offerId) {
            try {
                $offer = $this->offer->load((int)$offerId);
                if ($offer && $offer->getOfferId()) {
                    $label = $offer->getLabel();
                    $this->dataPersistor->set('offer', $offer->getData());
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ubarenroom_OfferManager::offer');
        $resultPage->getConfig()->getTitle()->prepend($label);

        return $resultPage;
    }
}
