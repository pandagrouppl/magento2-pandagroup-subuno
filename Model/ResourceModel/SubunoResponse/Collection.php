<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Model\ResourceModel\SubunoResponse;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use PandaGroup\Subuno\Model\ResourceModel\SubunoResponse as SubunoResponseResourceModel;
use PandaGroup\Subuno\Model\SubunoResponse;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(SubunoResponse::class, SubunoResponseResourceModel::class);
    }
}