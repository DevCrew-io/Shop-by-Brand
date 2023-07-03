<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Controller\Adminhtml\Index;

use Devcrew\Brand\Controller\Adminhtml\Brand;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * New Action controller class to add new brand
 *
 * Class NewAction
 */
class NewAction extends Brand
{
    /**
     * Execute method
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $this->_forward('edit');
    }

    /**
     * Resource access allowed or not
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Devcrew_Brand::add');
    }
}
