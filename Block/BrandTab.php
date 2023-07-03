<?php
/**
 * @author DevCrew Team
 * @copyriht Copyright (c) 2023 DevCrew {https://devcrew.io}
 * Created by PhpStorm
 * User: raheelshaukat
 * Date: 26/5/23
 * Time: 5:08 PM
 */

namespace Devcrew\Brand\Block;

use Devcrew\Brand\Api\Data\BrandInterface;
use Devcrew\Brand\Model\ResourceModel\Brand\Collection;

/**
 * BrandTab block class to show brands tab
 *
 * Class BrandTab
 */
class BrandTab extends Brand
{
    /**#@+
     * System configurations XML paths
     */
    const XML_PATH_BRANDS_BLOCK = 'devcrew_brand/brands_block/show_block';
    /**#@-*/

    /**
     * Get brands for home page
     *
     * @return Collection
     */
    public function getHomePageBrands()
    {
        $collection = $this->getBrandCollection();
        $collection->setOrder(BrandInterface::POSITION, 'ASC')
            ->setOrder(BrandInterface::TITLE, 'ASC')
            ->setPageSize(12);
        return $collection;
    }

    /**
     * Can show brands block
     *
     * @return mixed
     */
    public function canShowBlock()
    {
        return $this->_helper->getConfig(self::XML_PATH_BRANDS_BLOCK);
    }
}
