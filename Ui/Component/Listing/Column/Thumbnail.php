<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Ui\Component\Listing\Column;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Thumbnail UI class for thumbnail column shown in grid
 *
 * Class Thumbnail
 */
class Thumbnail extends Column
{
    /**
     * Constant for field alternative.
     */
    const ALT_FIELD = 'title';
    /**#@-*/

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $url = '';

                if ($item[$fieldName] != '') {
                    $url = $this->storeManager->getStore()->getBaseUrl(
                        UrlInterface::URL_TYPE_MEDIA
                    ) . $item[$fieldName];
                }

                $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
                $altField = $item[$altField] ?? '';

                $item[$fieldName . '_src'] = $url;
                $item[$fieldName . '_alt'] = $altField;
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'brands/index/edit',
                    ['brand_id' => $item['brand_id']]
                );
                $item[$fieldName . '_orig_src'] = $url;
            }
        }
        return $dataSource;
    }
}
