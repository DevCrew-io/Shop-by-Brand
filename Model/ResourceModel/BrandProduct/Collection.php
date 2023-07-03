<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Model\ResourceModel\BrandProduct;

use Devcrew\Brand\Model\Brand;
use Devcrew\Brand\Model\BrandProduct;
use Devcrew\Brand\Model\ResourceModel\BrandProduct as ResourceBrandProduct;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

/**
 * Collection class for brand products table
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
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            BrandProduct::class,
            ResourceBrandProduct::class
        );
    }

    /**
     * Return products against brand id
     *
     * @param int $brandId brandId
     * @return void
     */
    public function filterBrandProducts($brandId)
    {
        $brandTable = $this->getTable(Brand::TABLE_BRAND);
        $this->getSelect()
            ->join(
                ['brand' => $brandTable],
                'main_table.brand_id = brand.brand_id and brand.status = ' . Brand::STATUS_ENABLED
            );
        $this->getSelect()
            ->where('brand.brand_id = ' . $brandId);
    }

    /**
     * Return brands of given id
     *
     * @param int $productId
     * @return void
     */
    public function filterBrands($productId, $storeId)
    {
        $brandTable = $this->getTable(Brand::TABLE_BRAND);
        $brandStoreTable = $this->getTable(Brand::TABLE_BRAND_STORE);

        $this->getSelect()->join(
            ['brand' => $brandTable],
            'main_table.brand_id = brand.brand_id and brand.status = ' . Brand::STATUS_ENABLED
        );

        $this->getSelect()->join(
            ['brandStore' => $brandStoreTable],
            'main_table.brand_id = brandStore.brand_id '
        );

        $this->getSelect()
            ->where('main_table.product_id = ' . $productId . ' AND (brandStore.store_id = ' . $storeId
                . ' OR brandStore.store_id = 0 )');
    }
}
