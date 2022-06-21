<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Api;

interface SecretDataProviderInterface
{
    public const SECRET_DATA_DEFAULT_VALUE = '';

    /**
     * The secret data like cvv or avs responses are store in different ways depending on payment module that is in use.
     * To correctly pass cvv number, avs response or iin number - the Magento plugin should be written to the selected secret data provider class.
     *
     * @return string
     */
    public function get(): string;
}