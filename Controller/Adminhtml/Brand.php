<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Controller\Adminhtml;

use Devcrew\Brand\Api\BrandRepositoryInterface;
use Devcrew\Brand\Model\BrandFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

/**
 * Abstract controller class Brand
 *
 * Abstract Class Brand
 */
abstract class Brand extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var BrandFactory
     */
    protected $_brandFactory;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var BrandRepositoryInterface
     */
    protected $_brandRepository;

    /**
     * @var DataPersistorInterface
     */
    protected $_dataPersistor;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param BrandFactory $brandFactory
     * @param LoggerInterface $logger
     * @param DataPersistorInterface $dataPersistor
     * @param BrandRepositoryInterface $brandRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BrandFactory $brandFactory,
        LoggerInterface $logger,
        DataPersistorInterface $dataPersistor,
        BrandRepositoryInterface $brandRepository
    ) {
        parent::__construct($context);
        $this->_logger = $logger;
        $this->_brandFactory = $brandFactory;
        $this->_dataPersistor = $dataPersistor;
        $this->_brandRepository = $brandRepository;
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * Init action
     *
     * @return Page
     */
    protected function _initAction()
    {
        return $this->_resultPageFactory->create();
    }

    /**
     * Resource access allowed or not
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Devcrew_Brand::manage');
    }
}
