<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Controller\Index;

use Devcrew\Brand\Api\BrandRepositoryInterface;
use Devcrew\Brand\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class to render brand view page
 *
 * Class Index
 */
class View extends Action
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Data $dataHelper
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param StoreManagerInterface $storeManager
     * @param BrandRepositoryInterface $brandRepository
     */
    public function __construct(
        Data $dataHelper,
        Context $context,
        PageFactory $pageFactory,
        StoreManagerInterface $storeManager,
        BrandRepositoryInterface $brandRepository
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->brandRepository = $brandRepository;
    }

    /**
     * Execute method to render brand page
     *
     * @return ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {
        $errorMessage = '';
        $resultRedirect = $this->resultRedirectFactory->create();
        $brandId = $this->getRequest()->getParam('id');
        try {
            $brand = $this->brandRepository->getById($brandId);
            $storeIds = $brand->getStoreId();
            $currentStoreId = $this->storeManager->getStore()->getId();
        } catch (\Exception $exception) {
            $errorMessage = __('No brand exist against this request');
        }

        if (!isset($brand)) {
            $errorMessage = __('No brand exist against this request');
        }

        if ($errorMessage) {
            $this->messageManager->addErrorMessage($errorMessage);
            return $resultRedirect->setPath('/');
        }

        if (!in_array(0, $storeIds) && !in_array($currentStoreId, $storeIds)) {
            $errorMessage = __('No brand exist against this request');
        }

        if (!$this->dataHelper->isEnabled(ScopeInterface::SCOPE_STORE, $currentStoreId)) {
            $errorMessage = __('Brand extension is currently disabled');
        }

        if (!$brand->getStatus()) {
            $errorMessage = __('This brand is currently disabled');
        }

        if ($errorMessage) {
            $this->messageManager->addErrorMessage($errorMessage);
            return $resultRedirect->setPath('/');
        }

        return $this->pageFactory->create();
    }
}
