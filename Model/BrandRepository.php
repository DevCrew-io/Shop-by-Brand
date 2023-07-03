<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Model;

use Devcrew\Brand\Api\BrandRepositoryInterface;
use Devcrew\Brand\Api\Data\BrandInterface;
use Devcrew\Brand\Api\Data\BrandSearchResultsInterface;
use Devcrew\Brand\Api\Data\BrandSearchResultsInterfaceFactory;
use Devcrew\Brand\Model\ResourceModel\Brand as BrandResourceModel;
use Devcrew\Brand\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Brand repository class for CRUD operations
 *
 * Class BrandRepository
 */
class BrandRepository implements BrandRepositoryInterface
{
    /**
     * @var BrandFactory
     */
    private $brandFactory;

    /**
     * @var BrandResourceModel
     */
    private $brandResource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var BrandCollectionFactory
     */
    private $brandCollectionFactory;

    /**
     * @var CollectionProcessorInterface|null
     */
    private $collectionProcessor;

    /**
     * @var BrandSearchResultsInterfaceFactory
     */
    private $brandSearchResultsInterfaceFactory;

    /**
     * @param BrandFactory $brandFactory
     * @param BrandResourceModel $brandResource
     * @param StoreManagerInterface $storeManager
     * @param BrandCollectionFactory $brandCollectionFactory
     * @param BrandSearchResultsInterfaceFactory $brandSearchResultsInterfaceFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        BrandFactory           $brandFactory,
        BrandResourceModel     $brandResource,
        StoreManagerInterface  $storeManager,
        BrandCollectionFactory $brandCollectionFactory,
        BrandSearchResultsInterfaceFactory $brandSearchResultsInterfaceFactory,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->brandFactory = $brandFactory;
        $this->storeManager = $storeManager;
        $this->brandResource = $brandResource;
        $this->collectionProcessor = $collectionProcessor;
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->brandSearchResultsInterfaceFactory = $brandSearchResultsInterfaceFactory;
    }

    /**
     * Brand save method
     *
     * @param BrandInterface $brand
     * @return BrandInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(BrandInterface $brand)
    {
        if (empty($brand->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $brand->setStoreId($storeId);
        }
        try {
            $this->brandResource->save($brand);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                "Couldn't save the brand: %1",
                $exception->getMessage()
            ));
        }
        return $brand;
    }

    /**
     * Get brand by id
     *
     * @param string $id
     * @return BrandInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $brand = $this->brandFactory->create();
        $brand->load($id);
        if (!$brand->getId()) {
            throw new NoSuchEntityException(__("Brand with id '%1' doesn't exist.", $id));
        }
        return $brand;
    }

    /**
     * Load brand data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return BrandSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var BrandResourceModel\Collection $collection */
        $collection = $this->brandCollectionFactory->create();

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), true);
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->brandSearchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete brand using brand object
     *
     * @param BrandInterface $brand
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(BrandInterface $brand)
    {
        try {
            $this->brandResource->delete($brand);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
        return true;
    }

    /**
     * Delete brand by brand id
     *
     * @param string $id
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */

    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
