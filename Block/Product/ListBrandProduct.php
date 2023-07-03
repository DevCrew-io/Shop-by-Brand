<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Block\Product;

use Devcrew\Brand\Helper\Data as BrandHelper;
use Devcrew\Brand\Model\BrandRepository;
use Devcrew\Brand\Model\ResourceModel\BrandProduct\CollectionFactory as BrandProductsCollectionFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Url\Helper\Data;
use Magento\Rule\Model\Condition\Combine;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\Widget\Block\BlockInterface;
use Magento\Widget\Helper\Conditions;

/**
 * Brand's product listing
 *
 * Class ListBrandProduct
 */
class ListBrandProduct extends ListProduct implements BlockInterface
{
    /**
     * @var object
     */
    protected $currentBrand;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Builder
     */
    protected $sqlBuilder;

    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @var Conditions
     */
    protected $conditionsHelper;

    /**
     * @var BrandProductsCollectionFactory
     */
    protected $brandProductsCollectionFactory;

    /**
     * @var BrandRepository
     */
    protected $brandRepository;

    /**
     * @var BrandHelper
     */
    protected $helper;

    /**
     * @param Rule $rule
     * @param Data $urlHelper
     * @param Context $context
     * @param Builder $sqlBuilder
     * @param Resolver $layerResolver
     * @param PostHelper $postDataHelper
     * @param Conditions $conditionsHelper
     * @param BrandHelper $helper
     * @param BrandRepository $brandRepository
     * @param CollectionFactory $productCollectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param BrandProductsCollectionFactory $brandProductsCollectionFactory
     * @param array $data
     */
    public function __construct(
        Rule $rule,
        Data $urlHelper,
        Context $context,
        Builder $sqlBuilder,
        Resolver $layerResolver,
        PostHelper $postDataHelper,
        Conditions $conditionsHelper,
        BrandHelper $helper,
        BrandRepository $brandRepository,
        CollectionFactory $productCollectionFactory,
        CategoryRepositoryInterface $categoryRepository,
        BrandProductsCollectionFactory $brandProductsCollectionFactory,
        array $data = []
    ) {
        $this->rule = $rule;
        $this->helper = $helper;
        $this->sqlBuilder = $sqlBuilder;
        $this->brandRepository = $brandRepository;
        $this->conditionsHelper = $conditionsHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->brandProductsCollectionFactory = $brandProductsCollectionFactory;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * Prepare layout method
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $brandId = $this->getRequest()->getParam('id');
        if ($brandId) {
            try {
                $brand = $this->brandRepository->getById($brandId);
                $this->currentBrand = $brand;
                $this->pageConfig->getTitle()->set($brand->getTitle());
                $this->pageConfig->setMetaTitle($brand->getMetaTitle());
                $this->pageConfig->setKeywords($brand->getMetaKeywords());
                $this->pageConfig->setDescription($brand->getMetaDescription());

            } catch (\Exception $exception) {
                $this->_logger->error($exception->getMessage());
            }
        }
    }

    /**
     * @inheirtDoc
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            /** @var Collection $collection */
            $collection = $this->productCollectionFactory->create();
            $this->_catalogLayer->prepareProductCollection($collection);
            $collection->addStoreFilter();
            $this->prepareProductCollection($collection);
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

    /**
     * Get product collection
     *
     * @return Collection
     */
    public function getProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Use this method to apply manual filters
     *
     * @param Collection $collection
     * @return void
     * @throws LocalizedException
     */
    public function prepareProductCollection($collection)
    {
        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        try {
            $brandId = $this->_request->getParam('id');
            $brandProductsCollection = $this->brandProductsCollectionFactory->create();
            $brandProductsCollection->filterBrandProducts($brandId);
            $productIds = [];
            foreach ($brandProductsCollection as $value) {
                $productIds[] = $value['product_id'];
            }
            $collection->addAttributeToFilter('entity_id', ['in' => $productIds]);
        } catch (\Exception $exception) {
            $this->setTemplate(null);
            $this->setCustomTemplate(null);
            $this->_logger->error($exception->getMessage());
        }
    }

    /**
     * Get conditions
     *
     * @return Combine
     */
    protected function getConditions()
    {
        $conditions = $this->getData('conditions_encoded')
            ? $this->getData('conditions_encoded')
            : $this->getData('conditions');
        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }
        $this->rule->loadPost(['conditions' => $conditions]);
        return $this->rule->getConditions();
    }

    /**
     * Get brand banner image
     *
     * @return string
     */
    public function getBrandBanner()
    {
        $bannerImage = '';
        if ($this->currentBrand) {
            $bannerImage = $this->currentBrand->getBannerImage();
            $bannerImage = $this->helper->getImageSrc($bannerImage);
        }
        return $bannerImage;
    }

    /**
     * Get brand description
     *
     * @return string
     */
    public function getBrandDescription()
    {
        $description = '';
        if ($this->currentBrand) {
            $description = $this->currentBrand->getDescription();
        }
        return $description;
    }
}
