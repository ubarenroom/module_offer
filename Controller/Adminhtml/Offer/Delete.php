<?php
namespace Ubarenroom\OfferManager\Controller\Adminhtml\Offer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Ubarenroom\OfferManager\Api\OfferRepositoryInterface;

class Delete extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Admin Resource
     */
    public const ADMIN_RESOURCE = 'Ubarenroom_OfferManager::offer';

    /**
     * @param Context $context
     * @param OfferRepositoryInterface $offerRepository
     */
    public function __construct(
        protected Context $context,
        protected OfferRepositoryInterface $offerRepository
    ) {
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $offerId = $this->getRequest()->getParam('offer_id');
        if ($offerId) {
            try {
                $this->offerRepository->deleteById($offerId);
                $this->messageManager->addSuccessMessage(__('You deleted the offer.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['offer_id' => $offerId]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an offer to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
