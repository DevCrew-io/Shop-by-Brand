<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Block;

use Devcrew\Brand\Api\Data\BrandInterface;
use Devcrew\Brand\Model\ResourceModel\Brand\Collection;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * ListBrand block for rendering brands
 *
 * Class ListBrand
 */
class ListBrand extends Brand implements IdentityInterface
{
    const DEFAULT_CACHE_TAG = 'DEVCREW_BRAND_LIST';

    /**
     * Get cache lifetime
     *
     * @return bool|int|null
     */
    protected function getCacheLifetime()
    {
        return parent::getCacheLifetime() ?: 86400;
    }

    /**
     * Get cache key info
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $keyInfo =  parent::getCacheKeyInfo();
        $key = $this->getRequest()->getParam('keyword');
        $storeId = $this->_helper->getCurrentStoreId();
        if ($key && $storeId) {
            $key = $storeId . '-' . $key;
        }
        $keyInfo[] = $key;
        return $keyInfo;
    }

    /**
     * Get identities
     *
     * @return string[]
     */
    public function getIdentities()
    {
        $keyword = $this->getRequest()->getParam('keyword');
        $key = $this->_helper->getCurrentStoreId();
        if ($keyword) {
            $key = $key . '-' . $keyword;
        }
        return [self::DEFAULT_CACHE_TAG, self::DEFAULT_CACHE_TAG . '_' . $key];
    }

    /**
     * @inheirtDoc
     */
    protected function _prepareLayout()
    {
        $title = $this->_helper->getModuleTitle();
        $this->pageConfig->getTitle()->set($title);
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $baseUrl = '';
            try {
                $baseUrl = $this->_helper->getStore()->getBaseUrl();
            } catch (\Exception $exception) {
                $this->_logger->error($exception->getMessage());
            }
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $baseUrl
            ])->addCrumb('brand', $this->getBreadcrumbsData());
        }
        return parent::_prepareLayout();
    }

    /**
     * Get breadcrumbs data
     *
     * @return array
     */
    protected function getBreadcrumbsData()
    {
        $data = [
            'label' => __('Brand'),
            'title' => __('Brand')
        ];
        $data['link'] = $this->_helper->getBrandListUrl();
        return $data;
    }

    /**
     * Get searched brands
     *
     * @return Collection
     */
    public function getBrands()
    {
        $keyword = $this->getRequest()->getParam('keyword');
        $collection = $this->getBrandCollection();
        if ($keyword) {
            $collection->addFieldToFilter('title', ['like' => '%' . $keyword . '%']);
        }
        $collection->setOrder(BrandInterface::POSITION, 'ASC');
        $collection->setOrder(BrandInterface::TITLE, 'ASC');
        return $collection;
    }

    /**
     * Get brand products count
     *
     * @param int $brandId
     * @return int
     */
    public function getProductCount($brandId)
    {
        $brandProductsCollection = $this->_brandProductsCollectionFactory->create();
        $brandProductsCollection->filterBrandProducts($brandId);
        $productIds = [];
        foreach ($brandProductsCollection as $value) {
            $productIds[] = $value['product_id'];
        }

        $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addAttributeToFilter('entity_id', ['in' => $productIds]);
        return $productCollection->count();
    }
}
