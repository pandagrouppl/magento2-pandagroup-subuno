<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Api;

use Magento\Sales\Api\Data\OrderInterface;
use PandaGroup\SubunoApi\Contract\DataObjectInterface;

interface TransformerInterface
{
    public function transform(OrderInterface $order): ?DataObjectInterface;
}