<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Block\Adminhtml\Index\Edit\Tab;

use Devcrew\Brand\Api\BrandRepositoryInterface;
use Devcrew\Brand\Model\Brand;
use Devcrew\Brand\Model\BrandFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

/**
 * Mian class for main tab in form
 *
 * Class Main
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var Brand
     */
    protected $brand;

    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var BrandFactory
     */
    private $brandFactory;

    /**
     * @var CustomerGroup
     */
    protected $customerGroup;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Brand $brand
     * @param Store $systemStore
     * @param BrandFactory $brandFactory
     * @param CustomerGroup $customerGroup
     * @param DataPersistorInterface $dataPersistor
     * @param BrandRepositoryInterface $brandRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Brand $brand,
        Store $systemStore,
        BrandFactory $brandFactory,
        CustomerGroup $customerGroup,
        DataPersistorInterface $dataPersistor,
        BrandRepositoryInterface $brandRepository,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $data
        );
        $this->brand = $brand;
        $this->systemStore = $systemStore;
        $this->brandFactory = $brandFactory;
        $this->customerGroup = $customerGroup;
        $this->dataPersistor = $dataPersistor;
        $this->brandRepository = $brandRepository;
    }

    /**
     * Prepare Form
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $brand = $this->brandFactory->create();
        $brandId = $this->dataPersistor->get('current_brand_id');
        if ($brandId) {
            try {
                $brand = $this->brandRepository->getById($brandId);
            } catch (\Exception $exception) {
                $this->_logger->error($exception->getMessage());
            }
        }

        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'edit_form',
                'enctype'=>'multipart/form-data',
                'action' => $this->getData('action'),
                'method' => 'post'
            ]
        ]);

        $form->setHtmlIdPrefix('post_');
        $fieldset = $form->addFieldset(
            'brand_details',
            ['legend' => __('Brand Information'), 'class' => 'fieldset-wide']
        );

        if ($brand->getId()) {
            $fieldset->addField('brand_id', 'hidden', ['name' => 'brand_id']);
        }

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'values' => $this->brand->getAvailableStatuses()
            ]
        );

        if (!$brand->getId()) {
            $brand->setData('status', '1');
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'class' => 'required-entry',
                'required' => true
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description')
            ]
        );

        $fieldset->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key')
            ]
        );

        $fieldset->addField(
            'store_id',
            'multiselect',
            [
                'name'     => 'store_id[]',
                'label'    => __('Store Views'),
                'title'    => __('Store Views'),
                'required' => true,
                'values'   => $this->systemStore->getStoreValuesForForm(false, true),
            ]
        );

        $isRequired = (bool)$brand->getId();

        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Brand Logo'),
                'title' => __('Brand Logo'),
                'note' => __(
                    'Note : Please upload image of max size 250 px (width) x 170 px (height).'
                ),
                'class' =>'admin__control-image',
                'required' => $isRequired,
            ]
        )->setAfterElementHtml(
            '<script>
                require([
                    "jquery",
                ], function($){
                    $(document).ready(function () {
                        $("#post_image_delete").parent().hide();
                        if($("#post_image_image").attr("src")){
                            $("#post_image").removeClass("required-file");
                        }else{
                            $("#post_image").addClass("required-file");
                        }

                        $( "#post_image" ).attr(
                            "accept",
                            "image/x-png,image/gif,image/jpeg,image/jpg,image/png"
                        );
                    });
                  });
            </script>'
        );

        $fieldset->addField(
            'banner_image',
            'image',
            [
                'name' => 'banner_image',
                'label' => __('Banner Image'),
                'title' => __('Banner Image'),
                'class' =>'admin__control-image'
            ]
        )->setAfterElementHtml(
            '<script>
                require([
                    "jquery",
                ], function($){
                    $(document).ready(function () {
                        $( "#post_banner_image" ).attr(
                            "accept",
                            "image/x-png,image/gif,image/jpeg,image/jpg,image/png"
                        );
                    });
                  });
            </script>'
        );

        $fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'class'     => 'validate-number'
            ]
        );

        $fieldset->addField(
            'meta_title',
            'text',
            [
                'name' => 'meta_title',
                'label' => __('SEO Meta Title'),
                'title' => __('SEO Meta Title')
            ]
        );

        $fieldset->addField(
            'meta_keywords',
            'text',
            [
                'name' => 'meta_keywords',
                'label' => __('SEO Meta Keywords'),
                'title' => __('SEO Meta Keywords')
            ]
        );

        $fieldset->addField(
            'meta_description',
            'text',
            [
                'name' => 'meta_description',
                'label' => __('SEO Meta Description'),
                'title' => __('SEO Meta Description')
            ]
        );

        $form->setValues($brand->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Brand Information');
    }

    /**
     * Prepare title for tab
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('Brand Information');
    }

    /**
     * Check resource access allowed or not
     *
     * @param string $resourceId
     * @return boolean
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
