<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

declare(strict_types=1);

namespace Devcrew\Brand\Model\Source;

use Devcrew\Brand\Model\Brand;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Source class for status column in brand listing
 *
 * Class IsActive
 */
class IsActive implements OptionSourceInterface
{
    /**
     * @var Brand
     */
    private $brand;

    /**
     * @param Brand $brand
     */
    public function __construct(
        Brand $brand
    ) {
        $this->brand = $brand;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->brand->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
