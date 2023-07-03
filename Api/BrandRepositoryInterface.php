<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Api;

use Devcrew\Brand\Api\Data\BrandInterface;
use Devcrew\Brand\Api\Data\BrandSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface BrandRepositoryInterface
 */
interface BrandRepositoryInterface
{
    /**
     * Save Brand
     *
     * @param BrandInterface $brand
     * @return BrandInterface
     * @throws LocalizedException
     */
    public function save(BrandInterface $brand);

    /**
     * Get Brand by ID
     *
     * @param int $brandId
     * @return BrandInterface
     * @throws LocalizedException
     */
    public function getById($brandId);

    /**
     * Retrieve list matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return BrandSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Brand
     *
     * @param BrandInterface $brand
     * @return bool
     * @throws LocalizedException
     */
    public function delete(BrandInterface $brand);

    /**
     * Delete Brand by ID
     *
     * @param int $brandId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($brandId);
}
