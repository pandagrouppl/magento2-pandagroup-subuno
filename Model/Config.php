<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PandaGroup\SubunoApi\Config\AbstractConfig;

class Config extends AbstractConfig
{
    private const XML_PATH_BASE = 'subuno/';
    private const XML_PATH_ENABLED = self::XML_PATH_BASE . 'general/enable';
    private const XML_PATH_RUN = self::XML_PATH_BASE . 'general/run';
    private const XML_PATH_REJECT = self::XML_PATH_BASE . 'general/reject';
    private const XML_PATH_ERROR_MESSAGE = self::XML_PATH_BASE . 'general/error_message';
    private const XML_PATH_BASE_URI = self::XML_PATH_BASE . 'connection/base_uri';
    private const XML_PATH_API_KEY = self::XML_PATH_BASE . 'connection/api_key';
    private const XML_PATH_CRON_BATCH = self::XML_PATH_BASE . 'cron/batch';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED);
    }

    public function whenRun(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_RUN);
    }

    public function rejectAction(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_REJECT);
    }

    public function getErrorMessage(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ERROR_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function getBaseUri(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_BASE_URI);
    }

    /**
     * @inheritDoc
     */
    public function getApiKey(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_KEY);
    }

    public function getCronBatchSize(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_CRON_BATCH);
    }
}