<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Model;

use Devcrew\Brand\Api\Data\BrandInterface ;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class Brand
 *
 * Model class for brands
 */
class Brand extends AbstractModel implements BrandInterface, IdentityInterface
{
    /**
     * Constants for Brand status
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * Constants for table names.
     */
    const TABLE_BRAND = 'devcrew_brand';
    const TABLE_BRAND_PRODUCTS = 'devcrew_brand_products';
    const TABLE_BRAND_STORE = 'devcrew_brand_store';
    /**#@-*/

    /**
     * Constants for Url entity & extension
     */
    const URL_ENTITY = 'brand';
    const URL_EXT = '.html';
    const BRAND_VIEW_URL_PATH = 'devcrewbrand/index/view';
    /**#@-*/

    /**
     * Brand cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'devcrew_brand_grid';

    /**
     * Brand cache tag
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * EventPrefix
     *
     * @var string
     */
    protected $_eventPrefix = 'devcrew_brand_grid';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Js
     */
    private $jsHelper;

    public function __construct(
        Context $context,
        Registry $registry,
        Js $jsHelper,
        ResourceConnection $resourceConnection,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->jsHelper = $jsHelper;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Devcrew\Brand\Model\ResourceModel\Brand::class);
    }

    /**
     * {@inheritdoc}
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get products of brand
     *
     * @param Brand $object object
     * @return array
     */
    public function getProducts(Brand $object)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName(
            self::TABLE_BRAND_PRODUCTS
        );
        $select = $connection->select()
        ->from(
            $tableName,
            ['product_id']
        )->where(
            'brand_id = ?',
            (int)$object->getId()
        );
        return $connection->fetchCol($select);
    }

    /**
     * Save products
     *
     * @param Brand $model
     * @param mixed $brandProducts
     * @return bool
     */
    public function saveProducts($model, $brandProducts)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName(
                self::TABLE_BRAND_PRODUCTS
            );

            $newProducts = $this->jsHelper->decodeGridSerializedInput($brandProducts);
            $oldProducts = $model->getProducts($model);
            $deletedProductsArray = array_diff($oldProducts, $newProducts);
            $productsInDb = array_intersect($oldProducts, $newProducts);

            foreach ($productsInDb as $value) {
                $key = array_search($value, $newProducts);
                unset($newProducts[$key]);
            }

            if (!empty($deletedProductsArray)) {
                $where = [
                    'brand_id = ?' => (int)$model->getId(),
                    'product_id IN (?)' => $deletedProductsArray
                ];
                $connection->delete($tableName, $where);
            }

            if ($newProducts) {
                $data = [];
                foreach ($newProducts as $productId) {
                    $data[] = [
                        BrandInterface::BRAND_ID => (int)$model->getId(),
                        'product_id' => (int)$productId
                    ];
                }
                $connection->insertMultiple($tableName, $data);
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Get available status for enable & disable
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
    }

    /**
     * Receive brand store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : (array)$this->getData('store_id');
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return parent::getData(self::BRAND_ID);
    }

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);
    }

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return parent::getData(self::TITLE);
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return parent::getData(self::DESCRIPTION);
    }

    /**
     * Get url key
     *
     * @return string|null
     */
    public function getUrlKey()
    {
        return parent::getData(self::URL_KEY);
    }

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle()
    {
        return parent::getData(self::META_TITLE);
    }

    /**
     * Get meta keywords
     *
     * @return string|null
     */
    public function getMetaKeywords()
    {
        return parent::getData(self::META_KEYWORDS);
    }

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription()
    {
        return parent::getData(self::META_DESCRIPTION);
    }

    /**
     * Get Position
     *
     * @return string|null
     */
    public function getPosition()
    {
        return parent::getData(self::POSITION);
    }

    /**
     * Get image
     *
     * @return string|null
     */
    public function getImage()
    {
        return parent::getData(self::IMAGE);
    }

    /**
     * Get banner image
     *
     * @return string|null
     */
    public function getBannerImage()
    {
        return parent::getData(self::BANNER_IMAGE);
    }

    /**
     * Set Id
     *
     * @param int|null $id
     * @return BrandInterface|Brand
     */
    public function setId($id)
    {
        return $this->setData(self::BRAND_ID, $id);
    }

    /**
     * Set status
     *
     * @param $status
     * @return BrandInterface|Brand
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set Title
     *
     * @param string $title
     * @return BrandInterface|Brand
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set Description
     *
     * @param string $description
     * @return BrandInterface|Brand
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Set url key
     *
     * @param string $urlKey
     * @return BrandInterface|Brand
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * Set meta title
     *
     * @param string $metaTitle
     * @return BrandInterface|Brand
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    /**
     * Set meta keywords
     *
     * @param string $metaKeywords
     * @return BrandInterface|Brand
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return BrandInterface|Brand
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * Set position
     *
     * @param $position
     * @return BrandInterface|Brand
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * Set image
     *
     * @param $image
     * @return BrandInterface|Brand
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Set banner image
     *
     * @param $bannerImage
     * @return BrandInterface|Brand
     */
    public function setBannerImage($bannerImage)
    {
        return $this->setData(self::BANNER_IMAGE, $bannerImage);
    }
}
