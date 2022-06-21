<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SubunoResponse extends AbstractDb
{
    public const MAIN_TABLE = 'subuno_response';

    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE, 'id');
    }
}