<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface for brand search results.
 *
 * Interface BrandSearchResultsInterface
 */
interface BrandSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get brands list.
     *
     * @return ExtensibleDataInterface[]
     */
    public function getItems();

    /**
     * Set brands list.
     *
     * @param BrandInterface[] $items
     * @return BrandSearchResultsInterface
     */
    public function setItems(array $items);
}
