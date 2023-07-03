<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Model\ResourceModel\Brand;

use Devcrew\Brand\Api\Data\BrandInterface;
use Devcrew\Brand\Model\Brand;
use Devcrew\Brand\Model\ResourceModel\AbstractCollection;
use Devcrew\Brand\Model\ResourceModel\Brand as ResourceModelBrand;
use Magento\Store\Model\Store;

/**
 * Collection class for brand
 *
 * Class Collection
 */
class Collection extends AbstractCollection
{
    /**
     * Primary field name of table
     *
     * @var string
     */
    protected $_idFieldName = 'brand_id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Brand::class,
            ResourceModelBrand::class
        );
        $this->_map['fields']['brand_id'] = 'main_table.brand_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Add filter by store
     *
     * @param int|array|Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = false)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
            $this->setFlag('store_filter_added', 1);
        }
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @inheirtDoc
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(BrandInterface::class);
        $this->performAfterLoad(Brand::TABLE_BRAND_STORE, $entityMetadata->getLinkField());
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }

    /**
     * Render filters before
     *
     * @return void
     * @throws \Exception
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(BrandInterface::class);
        $this->joinStoreRelationTable(Brand::TABLE_BRAND_STORE, $entityMetadata->getLinkField());
    }
}
