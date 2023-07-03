<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Controller\Adminhtml\Index;

use Devcrew\Brand\Api\BrandRepositoryInterface;
use Devcrew\Brand\Controller\Adminhtml\Brand;
use Devcrew\Brand\Model\Brand as BrandModel;
use Devcrew\Brand\Model\BrandFactory;
use Devcrew\Brand\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Psr\Log\LoggerInterface;

/**
 * Mass Delete controller class to perform mass delete action
 *
 * Class MassDelete
 */
class MassDelete extends Brand
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var UrlPersistInterface
     */
    private $urlPersist;

    /**
     * @param Filter $filter
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param BrandFactory $brandFactory
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param UrlPersistInterface $urlPersist
     * @param DataPersistorInterface $dataPersistor
     * @param BrandRepositoryInterface $brandRepository
     */
    public function __construct(
        Filter                   $filter,
        Context                  $context,
        PageFactory              $resultPageFactory,
        BrandFactory             $brandFactory,
        LoggerInterface          $logger,
        CollectionFactory        $collectionFactory,
        UrlPersistInterface      $urlPersist,
        DataPersistorInterface   $dataPersistor,
        BrandRepositoryInterface $brandRepository
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $brandFactory,
            $logger,
            $dataPersistor,
            $brandRepository
        );
        $this->filter = $filter;
        $this->urlPersist = $urlPersist;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute method to perform brand mass delete action
     *
     * @return ResponseInterface|Redirect|(Redirect&ResultInterface)|ResultInterface|void
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
            $this->messageManager->addErrorMessage(
                __('Something went wrong. Please review the logs and try again.')
            );
            return $resultRedirect->setPath('*/*/');
        }

        $recordDeleted = 0;
        /** @var \Devcrew\Brand\Model\Brand $item */
        foreach ($collection as $item) {
            try {
                $this->_brandRepository->deleteById($item->getId());

                $this->urlPersist->deleteByData([
                    UrlRewrite::ENTITY_ID => $item->getId(),
                    UrlRewrite::ENTITY_TYPE => BrandModel::URL_ENTITY,
                    UrlRewrite::REDIRECT_TYPE => 0,
                    UrlRewrite::TARGET_PATH => BrandModel::BRAND_VIEW_URL_PATH . '/id/' . $item->getId()
                ]);

                $recordDeleted++;
            } catch (\Exception $exception) {
                $this->_logger->error($exception->getMessage());
            }
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $recordDeleted)
        );
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Resource access allowed or not
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Devcrew_Brand::save');
    }
}
