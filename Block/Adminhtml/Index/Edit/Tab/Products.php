<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Block\Adminhtml\Index\Edit\Tab;

use Devcrew\Brand\Api\BrandRepositoryInterface;
use Devcrew\Brand\Api\Data\BrandInterface;
use Devcrew\Brand\Model\Brand;
use Devcrew\Brand\Model\BrandFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Products block class for form tab products
 *
 * Class Products
 */
class Products extends Extended
{
    /**
     * @var CollectionFactory
     */
    protected $productCollection;

    /**
     * @var BrandFactory
     */
    protected $brandFactory;

    /**
     * @var BrandRepositoryInterface
     */
    protected $brandRepository;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var Visibility
     */
    private $visibility;

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param Status $status
     * @param Visibility $visibility
     * @param BrandFactory $brandFactory
     * @param CollectionFactory $productCollection
     * @param BrandRepositoryInterface $brandRepository
     * @param array $data
     */
    public function __construct(
        Context                  $context,
        Data                     $backendHelper,
        Status                   $status,
        Visibility               $visibility,
        BrandFactory             $brandFactory,
        CollectionFactory        $productCollection,
        BrandRepositoryInterface $brandRepository,
        array                    $data = []
    ) {
        parent::__construct(
            $context,
            $backendHelper,
            $data
        );
        $this->status = $status;
        $this->visibility = $visibility;
        $this->brandFactory = $brandFactory;
        $this->brandRepository = $brandRepository;
        $this->productCollection = $productCollection;
    }

    /**
     * Initialize block
     *
     * @return void
     * @throws FileSystemException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('brand_product_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('brand_id')) {
            $this->setDefaultFilter(['in_product' => 1]);
        }
    }

    /**
     * Add column filter to the collection
     *
     * @param Column $column
     * @return $this|Extended
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'include_products') {
            $brandProductIds = $this->_getSelectedProducts();
            $brandProductIds = $brandProductIds ?: 0;

            if ($column->getFilter()->getValue()) {
                $this->getCollection()
                    ->addFieldToFilter('entity_id', ['in' => $brandProductIds]);
            } else {
                if ($brandProductIds) {
                    $this->getCollection()
                        ->addFieldToFilter('entity_id', ['nin' => $brandProductIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Prepare Collection
     *
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->productCollection->create();
        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('visibility')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('price');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Columns
     *
     * @return Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'include_products',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'include_products',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => $this->_getSelectedProducts(),
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku'
            ]
        );
        $this->addColumn(
            'visibility',
            [
                'header' => __('Visibility'),
                'index' => 'visibility',
                'type' => 'options',
                'options' => $this->visibility->getOptionArray(),
                'header_css_class' => 'col-visibility',
                'column_css_class' => 'col-visibility'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->status->getOptionArray()
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'currency_code' => (string)$this->_scopeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'index' => 'price'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get products grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsgrid', ['_current' => true]);
    }

    /**
     * Get row url
     *
     * @param Product|DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * Get selected products
     *
     * @return mixed
     */
    protected function _getSelectedProducts()
    {
        return $this->getSelectedProducts();
    }

    /**
     * Get selected/checked product ids
     *
     * @return mixed
     */
    public function getSelectedProducts()
    {
        $brand = $this->getBrand();
        $brandProducts = $brand->getProducts($brand);

        if (!is_array($brandProducts)) {
            $brandProducts = [];
        }

        return $brandProducts;
    }

    /**
     * Get current Brand
     *
     * @return BrandInterface|Brand
     */
    protected function getBrand()
    {
        $brandId = $this->getRequest()->getParam('brand_id');
        $brand   = $this->brandFactory->create();

        if ($brandId) {
            try {
                $brand = $this->brandRepository->getById($brandId);
            } catch (\Exception $exception) {
                $this->_logger->error($exception->getMessage());
            }
        }

        return $brand;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isHidden()
    {
        return true;
    }
}
