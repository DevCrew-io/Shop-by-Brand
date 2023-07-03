<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Model\ResourceModel;

use Devcrew\Brand\Api\Data\BrandInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\AbstractModel;

/**
 * Resource model class for brand table
 *
 * Class Brand
 */
class Brand extends AbstractDb
{
    /**
     * Store model
     *
     * @var null|Store
     */
    protected $_store = null;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param string|null $resourcePrefix
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $resourcePrefix = null
    ) {
        $this->_storeManager = $storeManager;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        parent::__construct(
            $context,
            $resourcePrefix
        );
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('devcrew_brand', 'brand_id');
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(BrandInterface::class)->getEntityConnection();
    }

    /**
     * Load function
     *
     * @param AbstractModel $object
     * @param $value
     * @param $field
     * @return $this|Brand
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $this->entityManager->load($object, $value);
        return $this;
    }

    /**
     * Get store ids to which specified brand is assigned
     *
     * @param int $brandId
     * @return array
     * @throws LocalizedException
     */
    public function lookupStoreIds($brandId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(BrandInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['dbs' => $this->getTable('devcrew_brand_store')], 'store_id')
            ->join(
                ['db' => $this->getMainTable()],
                'dbs.' . $linkField . ' = db.' . $linkField,
                []
            )
            ->where('db.' . $entityMetadata->getIdentifierField() . ' = :brand_id');

        return $connection->fetchCol($select, ['brand_id' => (int)$brandId]);
    }

    /**
     * Set store model
     *
     * @param Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }
}
