<?php
/**
 * @author DevCrew Team
 * @copyriht Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\ViewModel;

use Devcrew\Brand\Helper\Data;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * View model for brands listing and view page
 *
 * Class Brand
 */
class Brand implements ArgumentInterface
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Get image source
     *
     * @param string|null $image
     * @return string
     */
    public function getImageSrc($image = null)
    {
        return $this->helper->getImageSrc($image);
    }

    /**
     * Get brand list URL
     *
     * @return string
     */
    public function getBrandListUrl()
    {
        return $this->helper->getBrandListUrl();
    }

    /**
     * Get brand URL
     *
     * @param string|null $brandKey
     * @return string
     */
    public function getBrandUrl($brandKey = null)
    {
        return $this->helper->getBrandUrl($brandKey);
    }

    /**
     * To check show brand listing page product count config enabled/disabled
     *
     * @return bool
     */
    public function showBrandListProductCount()
    {
        return $this->helper->showBrandListProductCount();
    }
}
