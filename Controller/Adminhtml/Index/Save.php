<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Controller\Adminhtml\Index;

use Devcrew\Brand\Api\BrandRepositoryInterface;
use Devcrew\Brand\Api\Data\BrandInterface;
use Devcrew\Brand\Controller\Adminhtml\Brand;
use Devcrew\Brand\Model\Brand as BrandModel;
use Devcrew\Brand\Model\BrandFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Uploader;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem\Io\File;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteFactory as ResourceUrlRewriteFactory;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Psr\Log\LoggerInterface;

/**
 * Save controller class to save the brand
 *
 * Class Save
 */
class Save extends Brand
{
    /**
     * Constants for images & url paths.
     */
    const IMAGE_PATH_BRAND = 'devcrew/brand';
    const IMAGE_PATH_BRAND_BANNER = 'devcrew/brand/banner';
    /**#@-*/

    /**
     * @var Js
     */
    private $jsHelper;

    /**
     * @var File
     */
    private $file;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var UrlRewriteFactory
     */
    private $urlRewriteFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlPersistInterface
     */
    private $urlPersist;

    /**
     * @var UrlRewriteCollectionFactory
     */
    private $urlRewriteCollectionFactory;

    /**
     * @var ResourceUrlRewriteFactory
     */
    private $resourceUrlRewriteFactory;

    /**
     * @var BrandModel
     */
    private $brandModel;

    /**
     * @param Js $jsHelper
     * @param File $file
     * @param Context $context
     * @param BrandModel $brandModel
     * @param PageFactory $resultPageFactory
     * @param BrandFactory $brandFactory
     * @param DirectoryList $directoryList
     * @param LoggerInterface $logger
     * @param UploaderFactory $uploaderFactory
     * @param ResourceConnection $resource
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param UrlPersistInterface $urlPersist
     * @param StoreManagerInterface $storeManager
     * @param DataPersistorInterface $dataPersistor
     * @param BrandRepositoryInterface $brandRepository
     * @param ResourceUrlRewriteFactory $resourceUrlRewriteFactory
     * @param UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     */
    public function __construct(
        Js                          $jsHelper,
        File                        $file,
        Context                     $context,
        BrandModel                  $brandModel,
        PageFactory                 $resultPageFactory,
        BrandFactory                $brandFactory,
        DirectoryList               $directoryList,
        LoggerInterface             $logger,
        UploaderFactory             $uploaderFactory,
        ResourceConnection          $resource,
        UrlRewriteFactory           $urlRewriteFactory,
        UrlPersistInterface         $urlPersist,
        StoreManagerInterface       $storeManager,
        DataPersistorInterface      $dataPersistor,
        BrandRepositoryInterface    $brandRepository,
        ResourceUrlRewriteFactory   $resourceUrlRewriteFactory,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $brandFactory,
            $logger,
            $dataPersistor,
            $brandRepository
        );
        $this->file = $file;
        $this->jsHelper = $jsHelper;
        $this->resource = $resource;
        $this->brandModel = $brandModel;
        $this->urlPersist = $urlPersist;
        $this->storeManager = $storeManager;
        $this->directoryList = $directoryList;
        $this->uploaderFactory = $uploaderFactory;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->resourceUrlRewriteFactory = $resourceUrlRewriteFactory;
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
    }

    /**
     * Save brand execute method
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $files = $this->getRequest()->getFiles();

        if (!$data) {
            return $resultRedirect->setPath('*/*/new');
        }

        if (empty($data[BrandInterface::URL_KEY])) {
            $data[BrandInterface::URL_KEY] = str_replace(
                ' ',
                '-',
                strtolower($data[BrandInterface::TITLE])
            );
        }

        $data['status'] = $data['status'] !== false ? 1 : 0;

        $model = $modelProduct = $this->_brandFactory->create();
        $model->setData($data);

        $urlExist = $isNewBrand = false;
        if (isset($data[BrandInterface::BRAND_ID])) {
            $model->setId($data[BrandInterface::BRAND_ID]);
            $modelProduct->setId($data[BrandInterface::BRAND_ID]);

            try {
                $brand = $this->_brandRepository->getById($data[BrandInterface::BRAND_ID]);
                $modelProduct = $brand;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(__('This brand no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            if ($brand->getUrlKey() != $model->getUrlKey()) {
                $isNewBrand = true;
                $urlExist = $this->checkUrlKeyExist(
                    $model->getUrlKey() . BrandModel::URL_EXT,
                    $model->getStoreId()
                );
            }
        } else {
            $urlExist = $this->checkUrlKeyExist(
                $model->getUrlKey() . BrandModel::URL_EXT,
                $model->getStoreId()
            );
        }

        if ($urlExist) {
            if ($isNewBrand) {
                $this->_dataPersistor->set('brand_form', $data);
                return $resultRedirect->setPath('*/*/edit', [
                    BrandInterface::BRAND_ID => $model->getId()
                ]);
            } else {
                return $resultRedirect->setPath('*/*/new');
            }
        }

        try {
            if (!empty($files[BrandInterface::IMAGE]['name'])) {
                $result = $this->uploadImage(BrandInterface::IMAGE);
                if (!$result) {
                    $this->_dataPersistor->set('brand_form', $data);
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [BrandInterface::BRAND_ID => $this->getRequest()->getParam(BrandInterface::BRAND_ID)]
                    );
                }
                $model->setImage(self::IMAGE_PATH_BRAND . '/' . $result['file']);
            } else {
                $model->setImage($data[BrandInterface::IMAGE]['value']);
            }

            if (!empty($files[BrandInterface::BANNER_IMAGE]['name'])) {
                if (isset($data[BrandInterface::BANNER_IMAGE]['delete'])) {
                    $this->file->rm(
                        $this->directoryList->getPath(DirectoryList::MEDIA) . '/'
                        . $data[BrandInterface::BANNER_IMAGE]['value']
                    );
                    $model->setData(BrandInterface::BANNER_IMAGE, '');
                } else {
                    $model->unsetData(BrandInterface::BANNER_IMAGE);
                }

                $result = $this->uploadImage(BrandInterface::BANNER_IMAGE);
                if (!$result) {
                    $this->_dataPersistor->set('brand_form', $data);
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [BrandInterface::BRAND_ID => $this->getRequest()->getParam(BrandInterface::BRAND_ID)]
                    );
                }
                $model->setBannerImage(self::IMAGE_PATH_BRAND_BANNER . '/' . $result['file']);
            } else {
                if (isset($data[BrandInterface::BANNER_IMAGE]['value'])
                    && !empty($data[BrandInterface::BANNER_IMAGE]['value'])) {
                    if (isset($data[BrandInterface::BANNER_IMAGE]['delete'])) {
                        $this->file->rm(
                            $this->directoryList->getPath(DirectoryList::MEDIA) . '/'
                            . $data[BrandInterface::BANNER_IMAGE]['value']
                        );
                        $model->setData(BrandInterface::BANNER_IMAGE, '');
                    } else {
                        $model->setBannerImage($data[BrandInterface::BANNER_IMAGE]['value']);
                    }
                }
            }

            $this->_brandRepository->save($model);
            $this->saveUrlRewrite($data, $model);

            if (isset($data['products'])) {
                $result = $this->brandModel->saveProducts($modelProduct, $data['products']);
                if (!$result) {
                    $this->messageManager->addErrorMessage(
                        __('Something went wrong while saving the contact.')
                    );
                }
            }

            if (!$this->messageManager->hasMessages()) {
                $this->messageManager->addSuccessMessage(
                    __('Brand has been saved successfully.')
                );
            }

            return $this->processResultRedirect($model, $resultRedirect, $data);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            if ($exception->getCode() != Uploader::TMP_NAME_EMPTY) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Please insert Image of types jpg, jpeg, gif, png')
                );
            } else {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while saving the brand.')
                );
            }
        }

        $this->_dataPersistor->set('brand_form', $data);
        return $resultRedirect->setPath(
            '*/*/edit',
            [BrandInterface::BRAND_ID => $this->getRequest()->getParam(BrandInterface::BRAND_ID)]
        );
    }

    /**
     * Check url key already exist or not
     *
     * @param string $urlKey
     * @param int $storeId
     * @return bool
     */
    private function checkUrlKeyExist($urlKey, $storeId)
    {
        $urlCollection = $this->urlRewriteCollectionFactory->create();
        $urlCollection->addFieldToFilter('request_path', ['eq' => $urlKey]);
        $urlCollection->addStoreFilter($storeId);
        if (count($urlCollection) > 0) {
            $this->messageManager->addErrorMessage(
                __('The value specified in the URL Key is already exists. Please use a unique url key')
            );
            return true;
        }

        return false;
    }

    /**
     * Upload image to brand directory
     *
     * @param string $imageField
     * @return array|bool
     */
    private function uploadImage(string $imageField)
    {
        $result = [];
        try {
            $path = '';
            if ($imageField == BrandInterface::IMAGE) {
                $path = $this->directoryList->getPath(DirectoryList::MEDIA) . '/' . self::IMAGE_PATH_BRAND;
            } elseif ($imageField == BrandInterface::BANNER_IMAGE) {
                $path = $this->directoryList->getPath(DirectoryList::MEDIA) . '/' . self::IMAGE_PATH_BRAND_BANNER;
            }

            if ($path) {
                $uploader = $this->uploaderFactory->create(['fileId' => $imageField]);
                $uploader->setAllowedExtensions([
                    'jpg',
                    'jpeg',
                    'gif',
                    'png'
                ])
                    ->setFilesDispersion(false)
                    ->setFilenamesCaseSensitivity(false)
                    ->setAllowRenameFiles(true);
                $result = $uploader->save($path);
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
            if ($exception->getCode() != Uploader::TMP_NAME_EMPTY) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Please insert Image of types jpg, jpeg, gif, png')
                );
            } else {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while saving the brand.')
                );
            }
        }
        return $result;
    }

    /**
     * Save URL rewrites
     *
     * @param array $data
     * @param BrandModel $model
     */
    private function saveUrlRewrite($data, $model)
    {
        try {
            $urlKey = $data['url_key'];
            $storeIds = $data['store_id'];
            if (in_array(0, $storeIds)) {
                $storeIds = [];
                $stores = $this->storeManager->getStores();
                foreach ($stores as $store) {
                    $storeIds[] = $store->getId();
                }
            }

            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID => $model->getId(),
                UrlRewrite::ENTITY_TYPE => BrandModel::URL_ENTITY,
                UrlRewrite::REDIRECT_TYPE => 0,
                UrlRewrite::TARGET_PATH => BrandModel::BRAND_VIEW_URL_PATH . '/id/' . $model->getId()
            ]);

            foreach ($storeIds as $key => $storeId) {
                $urlRewriteModel = $this->urlRewriteFactory->create();
                $resourceUrlRewriteModel = $this->resourceUrlRewriteFactory->create();

                $urlRewriteModel->setIdPath($model->getUrlKey() . '-' . uniqid())
                    ->setIsSystem(0)
                    ->setEntityType(BrandModel::URL_ENTITY)
                    ->setRequestPath($urlKey . BrandModel::URL_EXT)
                    ->setTargetPath(BrandModel::BRAND_VIEW_URL_PATH . '/id/' . $model->getId())
                    ->setRedirectType(0)
                    ->setStoreId($storeId)
                    ->setEntityId($model->getId());
                $resourceUrlRewriteModel->save($urlRewriteModel);
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Unable to save url key. %1', $exception->getMessage()));
            $this->_logger->error('Brand url rewrite saving issue:' . $exception->getMessage());
        }
    }

    /**
     * Process result redirect
     *
     * @param BrandInterface $model
     * @param Redirect $resultRedirect
     * @param array $data
     * @return mixed
     */
    private function processResultRedirect($model, $resultRedirect, $data)
    {
        try {
            if ($this->getRequest()->getParam('back', false) === 'duplicate') {
                $newEvent = $this->_brandFactory->create(['data' => $data]);
                $newEvent->setId(null);
                $urlKey = $model->getUrlKey() . '-' . uniqid();
                $newEvent->setUrlKey($urlKey);
                $newEvent->setStatus(BrandModel::STATUS_DISABLED);
                $newEvent->setImage($model->getImage());
                $newEvent->setBannerImage($model->getBannerImage());

                $this->_brandRepository->save($newEvent);
                $newData = [
                    'store_id' => $data['store_id'],
                    'url_key' => $urlKey
                ];
                $this->saveUrlRewrite($newData, $newEvent);
                $this->messageManager->addSuccessMessage(__('You duplicated the brand.'));
                return $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        BrandInterface::BRAND_ID => $newEvent->getId(),
                        '_current' => true
                    ]
                );
            }
            $this->_dataPersistor->clear('brand_form');
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath(
                    '*/*/edit',
                    [BrandInterface::BRAND_ID => $model->getId(), '_current' => true]
                );
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check resource access allowed or not
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Devcrew_Brand::save');
    }
}
