<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Helper;

use Devcrew\Brand\Model\Brand as BrandModel;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Helper class Data
 *
 * Class Data
 */
class Data extends AbstractHelper
{
    /**#@+
     * System configurations XML paths
     */
    public const XML_PATH_IS_ENABLED = 'devcrew_brand/general/enable';
    public const XML_PATH_TITLE = 'devcrew_brand/general/title';
    public const XML_PATH_SHOW_PRODUCT_COUNT = 'devcrew_brand/list_page_setting/show_product_count';
    /**#@-*/

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param UrlInterface $url
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        UrlInterface $url,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->url = $url;
        $this->storeManager = $storeManager;
    }

    /**
     * Get current store
     *
     * @return StoreInterface|string
     */
    public function getStore()
    {
        try {
            return $this->storeManager->getStore();
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return '';
    }

    /**
     * Get current store id
     *
     * @return int|string
     */
    public function getCurrentStoreId()
    {
        try {
            $store = $this->getStore();
            if ($store instanceof StoreInterface) {
                return $store->getId();
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return '';
    }

    /**
     * Return config value of given path
     *
     * @param string $configPath
     * @param string $scope
     * @param string|null $storeId
     * @return mixed
     */
    public function getConfig($configPath, $scope = ScopeInterface::SCOPE_STORE, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            $scope,
            $storeId
        );
    }

    /**
     * Check if MaintenancePage feature is enabled
     *
     * @param string $scope
     * @param string|null $storeId
     * @return boolean
     */
    public function isEnabled($scope = ScopeInterface::SCOPE_STORE, $storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_IS_ENABLED,
            $scope,
            $storeId
        );
    }

    /**
     * Get module title
     *
     * @param $scope
     * @param $storeId
     * @return mixed
     */
    public function getModuleTitle($scope = ScopeInterface::SCOPE_STORE, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TITLE,
            $scope,
            $storeId
        );
    }

    /**
     * To check show brand listing page product count config enabled/disabled
     *
     * @param string $scope
     * @param string|null $storeId
     * @return bool
     */
    public function showBrandListProductCount($scope = ScopeInterface::SCOPE_STORE, $storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_PRODUCT_COUNT,
            $scope,
            $storeId
        );
    }

    /**
     * Get image source
     *
     * @param string|null $image
     * @return string
     */
    public function getImageSrc($image = null)
    {
        try {
            if ($image) {
                return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                    . $image;
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return '';
    }

    /**
     * Get brand list URL
     *
     * @return string
     */
    public function getBrandListUrl()
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl() . 'devcrewbrand';
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return '';
    }

    /**
     * Get brand URL
     *
     * @param string|null $brandKey
     * @return string
     */
    public function getBrandUrl($brandKey = null)
    {
        try {
            if ($brandKey) {
                return $this->storeManager->getStore()->getBaseUrl() . $brandKey . BrandModel::URL_EXT;
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return '';
    }

    /**
     * Return product's grid url
     *
     * @return string
     */
    public function getProductsGridUrl()
    {
        return $this->url->getUrl(
            'devcrewbrand/index/products',
            ['_current' => true]
        );
    }
}
