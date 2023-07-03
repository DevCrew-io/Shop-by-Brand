<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Model\ResourceModel\Brand\Grid;

use Devcrew\Brand\Model\ResourceModel\Brand\Collection as BrandCollection;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Brand collection for grid
 *
 * Class Collection
 */
class Collection extends BrandCollection implements SearchResultInterface
{
    protected $aggregations;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param MetadataPool $metadataPool
     * @param $mainTable
     * @param $eventPrefix
     * @param $eventObject
     * @param $resourceModel
     * @param $model
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        MetadataPool $metadataPool,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = Document::class,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $metadataPool,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * Get aggregations
     *
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * Set aggregations
     *
     * @param AggregationInterface $aggregations
     * @return SearchResultInterface|void
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Retrieve all ids for collection
     * Backward compatibility with eav collection
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol(
            $this->_getAllIdsSelect($limit, $offset),
            $this->_bindParams
        );
    }

    /**
     * Get search criteria
     *
     * @return SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return $this|Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list
     *
     * @param ExtensibleDataInterface[] $items items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
}
