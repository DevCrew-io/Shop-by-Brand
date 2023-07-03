<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Block\Adminhtml\Index\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Exception\LocalizedException;

/**
 * Form class for brand form
 *
 * Class Form
 */
class Form extends Generic
{
    /**
     * Prepare Form
     *
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post' ,
                'enctype' => 'multipart/form-data'
            ]]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
