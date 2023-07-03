<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Controller\Adminhtml\Index;

use Devcrew\Brand\Controller\Adminhtml\Brand;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;

/**
 * Index controller class to show brands grid
 *
 * Class Edit
 */
class Index extends Brand
{
    /**
     * Execute method to show brands grid
     *
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Devcrew_Brand::manage');
        $resultPage->getConfig()->getTitle()->prepend((__('Manage Brands')));

        return $resultPage;
    }

    /**
     * Resource access allowed or not
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Devcrew_Brand::manage');
    }
}
