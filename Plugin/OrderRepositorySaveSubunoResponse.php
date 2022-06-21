<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use PandaGroup\Subuno\Api\SubunoResponseRepositoryInterface;

class OrderRepositorySaveSubunoResponse
{
    private SubunoResponseRepositoryInterface $repository;

    public function __construct(SubunoResponseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function afterSave(OrderRepositoryInterface $subject, OrderInterface $result): OrderInterface
    {
        $attributes = $result->getExtensionAttributes();
        $subunoResponse = $attributes->getSubunoResponse();
        if (null !== $subunoResponse) {
            $this->repository->save($subunoResponse);
        }

        return $result;
    }
}