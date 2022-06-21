<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Api\Data;

interface SubunoResponseInterface
{
    /**
     * @return string
     */
    public function getRawResponse(): string;

    /**
     * @param string $rawResponse
     * @return $this
     */
    public function setRawResponse(string $rawResponse): self;

    /**
     * @return string
     */
    public function getAction(): ?string;

    /**
     * @param string $action
     * @return $this
     */
    public function setAction(string $action): self;

    /**
     * @return string
     */
    public function getReferenceCode(): ?string;

    /**
     * @param string $referenceCode
     * @return $this
     */
    public function setReferenceCode(string $referenceCode): self;

    /**
     * @return string
     */
    public function getTransactionId(): ?string;

    /**
     * @param string $transactionId
     * @return $this
     */
    public function setTransactionId(string $transactionId): self;
}