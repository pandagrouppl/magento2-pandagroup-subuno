<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Service\Comparator;

class IsActionReject
{
    public function execute(?string $action): bool
    {
        return $action === 'reject';
    }
}