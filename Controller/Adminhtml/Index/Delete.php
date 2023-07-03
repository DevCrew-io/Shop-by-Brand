<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Controller\Adminhtml\Index;

use Devcrew\Brand\Api\BrandRepositoryInterface;
use Devcrew\Brand\Controller\Adminhtml\Brand;
use Devcrew\Brand\Model\Brand as BrandModel;
use Devcrew\Brand\Model\BrandFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Psr\Log\LoggerInterface;

/**
 * Delete controller class to delete a brand
 *
 * Class Delete
 */
class Delete extends Brand
{
    /**
     * @var UrlPersistInterface
     */
    private $urlPersist;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param BrandFactory $brandFactory
     * @param LoggerInterface $logger
     * @param UrlPersistInterface $urlPersist
     * @param DataPersistorInterface $dataPersistor
     * @param BrandRepositoryInterface $brandRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BrandFactory $brandFactory,
        LoggerInterface $logger,
        UrlPersistInterface $urlPersist,
        DataPersistorInterface $dataPersistor,
        BrandRepositoryInterface $brandRepository
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $brandFactory,
            $logger,
            $dataPersistor,
            $brandRepository
        );
        $this->urlPersist = $urlPersist;
    }

    /**
     * Execute method to delete a brand
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('brand_id');
        if ($id) {
            try {
                /** @var BrandModel $model */
                $model = $this->_brandRepository->getById($id);
                $this->_brandRepository->deleteById($model->getId());

                $this->urlPersist->deleteByData([
                    UrlRewrite::ENTITY_ID => $model->getId(),
                    UrlRewrite::ENTITY_TYPE => BrandModel::URL_ENTITY,
                    UrlRewrite::REDIRECT_TYPE => 0,
                    UrlRewrite::TARGET_PATH => BrandModel::BRAND_VIEW_URL_PATH . '/id/' . $model->getId()
                ]);

                $this->messageManager->addSuccessMessage(__('Brand was successfully deleted'));
                return $this->_redirect('devcrewbrand/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete item right now. Please review the logs and try again.')
                );
                $this->_logger->critical($exception->getMessage());
                $this->_redirect(
                    'devcrewbrand/*/edit',
                    ['brand_id' => $this->getRequest()->getParam('brand_id')]
                );
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an item to delete.'));
        $this->_redirect('devcrewbrand/*/');
    }

    /**
     * Resource access allowed or not
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Devcrew_Brand::delete');
    }
}
