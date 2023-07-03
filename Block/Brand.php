<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Block;

use Devcrew\Brand\Helper\Data;
use Devcrew\Brand\Model\Brand as BrandModel;
use Devcrew\Brand\Model\ResourceModel\Brand\Collection;
use Devcrew\Brand\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;
use Devcrew\Brand\Model\ResourceModel\BrandProduct\CollectionFactory as BrandProductsCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Brand class parent block
 *
 * Class Brand
 */
class Brand extends Template
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var SerializerInterface
     */
    protected $_serializer;

    /**
     * @var Collection
     */
    protected $_brandCollection;

    /**
     * @var BrandProductsCollectionFactory
     */
    protected $_brandProductsCollectionFactory;

    /**
     * @var BrandCollectionFactory
     */
    protected $_brandCollectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @param Context $context
     * @param Data $helper
     * @param SerializerInterface $serializer
     * @param BrandCollectionFactory $brandCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param BrandProductsCollectionFactory $brandProductsCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context                        $context,
        Data                           $helper,
        SerializerInterface            $serializer,
        BrandCollectionFactory         $brandCollectionFactory,
        ProductCollectionFactory       $productCollectionFactory,
        BrandProductsCollectionFactory $brandProductsCollectionFactory,
        array                          $data = []
    ) {
        $this->_helper = $helper;
        $this->_serializer = $serializer;
        $this->_brandCollectionFactory = $brandCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_brandProductsCollectionFactory = $brandProductsCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get responsive break points
     *
     * @return string[]
     */
    public function getResponsiveBreakpoints()
    {
        return [
            1921 => 'visible',
            1920 => 'widescreen',
            1480 => 'desktop',
            1200 => 'laptop',
            992 => 'notebook',
            768 => 'tablet',
            576 => 'landscape',
            481 => 'portrait',
            361 => 'mobile',
            1 => 'mobile'
        ];
    }

    /**
     * Get slide options
     *
     * @return string[]
     */
    public function getSlideOptions()
    {
        return [
            'autoplay',
            'arrows',
            'autoplay-speed',
            'dots',
            'infinite',
            'padding',
            'vertical',
            'vertical-swiping',
            'responsive',
            'rows',
            'slides-to-show',
            'swipe-to-slide'
        ];
    }

    /**
     * Get slider options
     *
     * @return string[]
     */
    public function getSliderOptions()
    {
        if ($this->getSlide()) {
            return $this->getSlideOptions();
        }

        $this->addData(['responsive' => $this->_serializer->serialize($this->getGridOptions())]);
        return ['padding', 'responsive'];
    }

    /**
     * Get grid options
     *
     * @return array
     */
    public function getGridOptions()
    {
        $options = [];
        $breakpoints = $this->getResponsiveBreakpoints();
        ksort($breakpoints);
        foreach ($breakpoints as $size => $screen) {
            $options[] = [$size-1 => $this->getData($screen)];
        }
        return $options;
    }

    /**
     * Get brand collection (active)
     *
     * @return Collection
     */
    public function getBrandCollection()
    {
        if (!$this->_brandCollection) {
            $collection = $this->_brandCollectionFactory->create()
                ->addFieldToFilter('status', BrandModel::STATUS_ENABLED);
            //$storeId = $this->_helper->getCurrentStoreId();
            //if ($storeId) {
            //    $collection->addStoreFilter($storeId);
            //}
            $this->_brandCollection = $collection;
        }

        return $this->_brandCollection;
    }

    /**
     * Get image source
     *
     * @param string|null $image
     * @return string
     */
    public function getImageSrc($image = null)
    {
        return $this->_helper->getImageSrc($image);
    }

    /**
     * Get brand URL
     *
     * @param string|null $brandKey
     * @return string
     */
    public function getBrandUrl($brandKey = null)
    {
        return $this->_helper->getBrandUrl($brandKey);
    }

    /**
     * Get brands listing URL
     *
     * @return string
     */
    public function getBrandListURL()
    {
        return $this->_helper->getBrandListUrl();
    }
}
