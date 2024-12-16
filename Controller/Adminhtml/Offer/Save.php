<?php
namespace Ubarenroom\OfferManager\Controller\Adminhtml\Offer;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Ubarenroom\OfferManager\Api\OfferRepositoryInterface;
use Ubarenroom\OfferManager\Model\Offer;
use Ubarenroom\OfferManager\Model\OfferFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Ubarenroom\OfferManager\Model\ImageUploader;
use Magento\Framework\Filter\FilterInput;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Save Offer action.
 */
class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{

    /**
     * Admin Resource
     */
    public const ADMIN_RESOURCE = 'Ubarenroom_OfferManager::offer';

    /**
     * @var TimezoneInterface|mixed
     */
    private $timezone;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param OfferFactory $offerFactory
     * @param OfferRepositoryInterface $offerRepository
     * @param ImageUploader $imageUploader
     * @param Date $filterDate
     * @param TimezoneInterface|null $timezone
     */
    public function __construct(
        protected Context $context,
        protected DataPersistorInterface $dataPersistor,
        protected OfferFactory $offerFactory,
        protected OfferRepositoryInterface $offerRepository,
        protected ImageUploader $imageUploader,
        private Date $filterDate,
        TimezoneInterface $timezone = null,
    ) {
        parent::__construct($context);
        $this->timezone =  $timezone ?? \Magento\Framework\App\ObjectManager::getInstance()->get(
            TimezoneInterface::class
        );
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (empty($data['offer_id'])) {
                $data['offer_id'] = null;
            }

            if (empty($data['start_date'])) {
                $data['start_date'] = $this->timezone->formatDate();
            }

            $filterValues = ['start_date' => $this->filterDate];
            if ($this->getRequest()->getParam('end_date')) {
                $filterValues['end_date'] = $this->filterDate;
            }
            $inputFilter = new FilterInput(
                $filterValues,
                [],
                $data
            );
            $data = $inputFilter->getUnescaped();
            $data = $this->manageImage($data);
            /** @var Offer $model */
            $model = $this->offerFactory->create();
            $offerId = $this->getRequest()->getParam('offer_id');
            if ($offerId) {
                try {
                    $model = $this->offerRepository->getById($offerId);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This offer no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            try {
                $model->setData($data);
                $model->setCategoryIds($data['category_ids']);
                $offerCreated = $this->offerRepository->save($model);
                if ($offerCreated) {
                    $offerId = $offerCreated->getOfferId();
                }
                $this->dataPersistor->clear('offer');
                $this->messageManager->addSuccessMessage(__('You saved the offer.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the offer.'));
                return $resultRedirect->setPath('*/*/');
            }

            $this->dataPersistor->set('offer', $data);
            return $resultRedirect->setPath('*/*/edit', ['offer_id' => $offerId]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Manage Image
     *
     * @param array $postData
     * @return mixed
     * @throws LocalizedException
     */
    private function manageImage($postData)
    {
        $data = $postData;
        if (isset($data['image']) && is_array($data['image'])) {
            if (!empty($data['image']['delete'])) {
                $data['image'] = null;
            } else {
                if (isset($data['image'][0]['name']) && isset($data['image'][0]['tmp_name'])) {
                    $imageUrl = $postData['image'][0]['url'];
                    $imageName = $postData['image'][0]['name'];
                    $data['image'] = $this->imageUploader->saveMediaImage($imageName, $imageUrl);
                } else {
                    unset($data['image']);
                }
            }
        }
        return $data;
    }
}
