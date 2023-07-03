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
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Mass Disable controller class to perform mass disable action
 *
 * Class MassDisable
 */
class MassDisable extends Brand
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
     * @param Filter $filter
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param BrandFactory $brandFactory
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
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
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute method to perform brand mass disable action
     *
     * @return ResponseInterface|Redirect|(Redirect&ResultInterface)|ResultInterface
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

        $recordDisabled = 0;
        /** @var BrandModel $item */
        foreach ($collection as $item) {
            try {
                $item->setStatus(BrandModel::STATUS_DISABLED);
                $this->_brandRepository->save($item);
                $recordDisabled++;
            } catch (\Exception $exception) {
                $this->_logger->error($exception->getMessage());
            }
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been disabled.', $recordDisabled)
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
