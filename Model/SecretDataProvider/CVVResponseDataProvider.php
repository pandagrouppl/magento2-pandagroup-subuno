<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Model\SecretDataProvider;

use PandaGroup\Subuno\Api\SecretDataProviderInterface;

class CVVResponseDataProvider implements SecretDataProviderInterface
{
    public function get(): string
    {
        return self::SECRET_DATA_DEFAULT_VALUE;
    }
}