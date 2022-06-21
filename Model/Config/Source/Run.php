<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Run implements OptionSourceInterface
{
    public const RUN_DURING_CHECKOUT = 1;
    public const RUN_ASYNC = 2;

    public function toOptionArray(): array
    {
        return [
            ['value' => self::RUN_DURING_CHECKOUT, 'label' => __('Run Subuno check during checkout')],
            ['value' => self::RUN_ASYNC, 'label' => __('Run Subuno check asynchronously')],
        ];
    }
}