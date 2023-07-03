<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Block\Adminhtml\Index\Edit;

use Magento\Backend\Block\Widget\Tabs as MainTabs;

/**
 * Block class for form tabs
 *
 * Class Tabs
 */
class Tabs extends MainTabs
{
    /**
     * Initialize Tabs Block
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('brand_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Brand Details'));
    }
}
