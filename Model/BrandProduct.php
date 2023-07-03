<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class BrandProduct
 *
 * Model class for brands
 */
class BrandProduct extends AbstractModel
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\BrandProduct::class);
    }
}
