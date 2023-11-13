<?php

namespace Mage4\Turnstile\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

final class Theme implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'light', 'label' => __('Light')],
            ['value' => 'dark', 'label' => __('Dark')],
            ['value' => 'auto', 'label' => __('Auto')]
        ];
    }
}
