<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Model\ResourceModel\Brand\Relation\Store;

use Devcrew\Brand\Api\Data\BrandInterface;
use Devcrew\Brand\Model\ResourceModel\Brand;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Store save handler class
 *
 * Class SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Brand
     */
    protected $resourceBrand;

    /**
     * @param Brand $resourceBrand
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        Brand $resourceBrand,
        MetadataPool $metadataPool
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceBrand = $resourceBrand;
    }

    /**
     * Execute method to save store
     *
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(BrandInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();
        $oldStores = $this->resourceBrand->lookupStoreIds((int)$entity->getId());
        $newStores = (array)$entity->getStores();

        if (empty($newStores)) {
            $newStores = (array)$entity->getStoreId();
        }

        $table = $this->resourceBrand->getTable('devcrew_brand_store');
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'store_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    $linkField => (int)$entity->getData($linkField),
                    'store_id' => (int)$storeId
                ];
            }
            $connection->insertMultiple($table, $data);
        }
        return $entity;
    }
}
