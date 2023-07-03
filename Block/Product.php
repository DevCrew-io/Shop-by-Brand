<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Block;

use Devcrew\Brand\Helper\Data;
use Devcrew\Brand\Model\ResourceModel\BrandProduct\CollectionFactory as BrandProductsCollectionFactory;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;

/**
 * Product block class for showing brand info on product detail page
 *
 * Class Product
 */
class Product extends AbstractProduct
{
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var BrandProductsCollectionFactory
     */
    private $brandProductsCollectionFactory;

    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param BrandProductsCollectionFactory $brandProductsCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context                        $context,
        Data                           $dataHelper,
        BrandProductsCollectionFactory $brandProductsCollectionFactory,
        array                          $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->dataHelper = $dataHelper;
        $this->brandProductsCollectionFactory = $brandProductsCollectionFactory;
    }

    /**
     * Get brands of current product
     *
     * @return mixed
     */
    public function getBrand()
    {
        $productId = $this->getRequest()->getParam('id');
        $currentStore = $this->dataHelper->getCurrentStoreId();
        $collection = $this->brandProductsCollectionFactory->create();
        $collection->filterBrands($productId, $currentStore);
        return $collection->fetchItem();
    }

    /**
     * Get image url of brand
     *
     * @param string $image
     * @return string
     */
    public function getBrandImageUrl($image)
    {
        return $this->dataHelper->getImageSrc($image);
    }

    /**
     * Get brand url
     *
     * @param string $brandKey
     * @return string
     */
    public function getBrandUrl($brandKey = '')
    {
        return $this->dataHelper->getBrandUrl($brandKey);
    }
}
