<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Index class controller
 *
 * Class Index
 */
class Index extends Container
{
    /**
     * Index Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Check user is allowed to access resource
     *
     * @param int $resourceId resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
