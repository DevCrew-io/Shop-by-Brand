<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Controller\Adminhtml\Index;

use Devcrew\Brand\Controller\Adminhtml\Brand;
use Devcrew\Brand\Model\Brand as BrandModel;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;

/**
 * Edit controller class to add/update brand
 *
 * Class Edit
 */
class Edit extends Brand
{
    /**
     * Execute method to add/update a brand
     *
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('brand_id');
        $resultPage = $this->_resultPageFactory->create();
        if ($id) {
            try {
                /** @var BrandModel $model */
                $model = $this->_brandRepository->getById($id);

                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This item no longer exists.'));
                    return $this->_redirect('brand/*');
                }
            } catch (\Exception $exception) {
                $this->_logger->error($exception->getMessage());
                $this->messageManager->addErrorMessage(__('This item no longer exists.'));
                return $this->_redirect('brand/*');
            }
            $this->_dataPersistor->set('current_brand_id', $id);

            $resultPage->addBreadcrumb(__('Edit Brand'), __('Edit Brand'));
            $resultPage->getConfig()->getTitle()->prepend(
                $model->getTitle()
            );
        } else {
            $resultPage->addBreadcrumb(__('New Brand'), __('New Brand'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Brand'));

            $this->_dataPersistor->clear('current_brand_id');
        }
        return $resultPage;
    }

    /**
     * Resource access allowed or not
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Devcrew_Brand::edit');
    }
}
