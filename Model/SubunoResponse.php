<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Model;

use Magento\Framework\Model\AbstractModel;
use PandaGroup\Subuno\Api\Data\SubunoResponseInterface;
use PandaGroup\Subuno\Model\ResourceModel\SubunoResponse as SubunoResponseResourceModel;

class SubunoResponse extends AbstractModel implements SubunoResponseInterface
{
    protected function _construct()
    {
        $this->_init(SubunoResponseResourceModel::class);
    }

    public function getRawResponse(): string
    {
        return $this->getData('raw_response');
    }

    public function setRawResponse(string $rawResponse): SubunoResponseInterface
    {
        return $this->setData('raw_response', $rawResponse);
    }

    public function getAction(): ?string
    {
        return $this->getData('action');
    }

    public function setAction(string $action): SubunoResponseInterface
    {
        return $this->setData('action', $action);
    }

    public function getReferenceCode(): string
    {
        return $this->getData('reference_code');
    }

    public function setReferenceCode(string $referenceCode): SubunoResponseInterface
    {
        return $this->setData('reference_code', $referenceCode);
    }

    public function getTransactionId(): string
    {
        return $this->getData('transaction_id');
    }

    public function setTransactionId(string $transactionId): SubunoResponseInterface
    {
        return $this->setData('transaction_id', $transactionId);
    }
}