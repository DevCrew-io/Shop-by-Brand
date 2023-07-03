<?php
/**
 * @author DevCrew Team
 * @copyriht Copyright (c) 2023 DevCrew {https://devcrew.io}
 * Created by PhpStorm
 * User: raheelshaukat
 * Date: 29/3/23
 * Time: 12:14 PM
 */

namespace Devcrew\Brand\Model;

use Devcrew\Brand\Api\Data\BrandSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Service data object with brand search results.
 *
 * Class BrandSearchResults
 */
class BrandSearchResults extends SearchResults implements BrandSearchResultsInterface
{
}
