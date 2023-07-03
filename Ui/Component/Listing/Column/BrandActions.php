<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\Escaper;

/**
 * BrandAction UI class for brand's grid action columns options
 *
 * Class BrandActions
 */
class BrandActions extends Column
{
    /**
     * Constants for url path actions.
     */
    const BRAND_EDIT_URL_PATH = 'devcrewbrand/index/edit';
    const BRAND_DELETE_URL_PATH = 'devcrewbrand/index/delete';
    /**#@-*/

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param Escaper $escaper
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        Escaper $escaper,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->escaper = $escaper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare data source for action column
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['brand_id'])) {
                    $title = $this->escaper->escapeHtmlAttr($item['title']);
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::BRAND_EDIT_URL_PATH,
                            ['brand_id' => $item['brand_id']]
                        ),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::BRAND_DELETE_URL_PATH,
                            ['brand_id' => $item['brand_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete %1', $title),
                            'message' => __(
                                'Are you sure you want to delete a %1 record?',
                                $title
                            )
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
