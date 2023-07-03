<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Block\Adminhtml\Index;

use Devcrew\Brand\Api\BrandRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Phrase;

/**
 * Edit class controller
 *
 * Class Edit
 */
class Edit extends Container
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param Context $context
     * @param Escaper $escaper
     * @param DataPersistorInterface $dataPersistor
     * @param BrandRepositoryInterface $brandRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Escaper $escaper,
        DataPersistorInterface $dataPersistor,
        BrandRepositoryInterface $brandRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->escaper = $escaper;
        $this->dataPersistor = $dataPersistor;
        $this->brandRepository = $brandRepository;
    }

    /**
     * Initialize blog Brand edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'brand_id';
        $this->_blockGroup = 'Devcrew_Brand';
        $this->_controller = 'adminhtml_index';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Brand'));
        $this->buttonList->add(
            'save_and_continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ],
                    ],
                ]
            ],
            -100
        );

        if ($this->isAllowedAction('Devcrew_Brand::delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Brand'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded brand
     *
     * @return Phrase|string
     */
    public function getHeaderText()
    {
        $brandId = $this->dataPersistor->get('current_brand_id');
        $brandTitle = '';
        if ($brandId) {
            try {
                $brand = $this->brandRepository->getById($brandId);
                $brandTitle = $brand->getTitle();
            } catch (\Exception $exception) {
                $this->_logger->error($exception->getMessage());
            }
        }

        if ($brandTitle) {
            return __(
                "Edit Brand '%1'",
                $this->escaper->escapeHtml($brandTitle)
            );
        } else {
            return __('New Brand');
        }
    }

    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/save', [
            '_current' => true,
            'back' => 'duplicate'
        ]);
    }

    /**
     * Check resource access allowed or not
     *
     * @param string $resourceId resourceId
     * @return bool
     */
    protected function isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
