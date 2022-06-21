<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Rejects implements OptionSourceInterface
{
    public const ORDER_ON_HOLD = 1;
    public const ORDER_CANCEL = 2;
    public const ORDER_ERROR = 3;

    public function toOptionArray(): array
    {
        return [
            ['value' => self::ORDER_ON_HOLD, 'label' => __('Put the order on hold')],
            ['value' => self::ORDER_CANCEL, 'label' => __('Cancel the order')],
            ['value' => self::ORDER_ERROR, 'label' => __('Throw error and do not save the transaction')]
        ];
    }
}