<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Controller\Adminhtml\Index;

use Devcrew\Brand\Api\BrandRepositoryInterface;
use Devcrew\Brand\Controller\Adminhtml\Brand;
use Devcrew\Brand\Model\BrandFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

/**
 * Products controller class for products
 *
 * Class Products
 */
class Products extends Brand
{
    /**
     * @var LayoutFactory
     */
    private $resultLayoutFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param BrandFactory $brandFactory
     * @param LayoutFactory $resultLayoutFactory
     * @param LoggerInterface $logger
     * @param DataPersistorInterface $dataPersistor
     * @param BrandRepositoryInterface $brandRepository
     */
    public function __construct(
        Context                  $context,
        PageFactory              $resultPageFactory,
        BrandFactory             $brandFactory,
        LayoutFactory            $resultLayoutFactory,
        LoggerInterface          $logger,
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
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Execute method to show products
     *
     * @return ResponseInterface|ResultInterface|Layout
     */
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()
            ->getBlock('devcrew.brand.edit.products')
            ->setInProducts(
                $this->getRequest()->getPost('brand_products')
            );
        return $resultLayout;
    }
}
