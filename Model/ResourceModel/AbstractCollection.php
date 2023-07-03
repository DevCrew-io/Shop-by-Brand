<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Model\ResourceModel;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as MainAbstractCollection;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Abstract collection of Brand and Brand Products
 *
 * Class AbstractCollection
 */
abstract class AbstractCollection extends MainAbstractCollection
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param MetadataPool $metadataPool
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        MetadataPool $metadataPool,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->metadataPool = $metadataPool;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     * @throws NoSuchEntityException
     */
    protected function performAfterLoad($tableName, $linkField)
    {
        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['devcrew_brand_store' => $this->getTable($tableName)])
                ->where('devcrew_brand_store.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData['store_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $storeIdKey = array_search(Store::DEFAULT_STORE_ID, $storesData[$linkedId], true);
                    if ($storeIdKey !== false) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = current($storesData[$linkedId]);
                        $storeCode = $this->storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
        }
    }

    /**
     * Perform adding filter by store
     *
     * @param int|array|Store $store
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddStoreFilter($store, $withAdmin = false)
    {
        if ($store instanceof Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store', $store, 'in');
    }

    /**
     * Add filter by store
     *
     * @param int|array|Store $store
     * @param bool            $withAdmin
     *
     * @return $this
     */
    abstract public function addStoreFilter($store, $withAdmin = true);

    /**
     * Join store relation table if there is store filter
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function joinStoreRelationTable($tableName, $linkField)
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable($tableName)],
                'main_table.' . $linkField . ' = store_table.' . $linkField,
                []
            )->group(
                'main_table.' . $linkField
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Select::GROUP);
        return $countSelect;
    }
}
