<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Model\ResourceModel\Brand\Relation\Store;

use Devcrew\Brand\Model\ResourceModel\Brand;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Store read handler class
 *
 * Class ReadHandler
 */
class ReadHandler implements ExtensionInterface
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
     * @param MetadataPool $metadataPool
     * @param Brand $resourceBrand
     */
    public function __construct(
        MetadataPool $metadataPool,
        Brand $resourceBrand
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceBrand = $resourceBrand;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $stores = $this->resourceBrand->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
        }
        return $entity;
    }
}
