<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Plugin;

use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;

class SalesOrderGridLoadSubunoResponse
{
    public function afterGetReport(CollectionFactory $subject, Collection $result, string $requestName): Collection
    {
        if ($requestName == 'sales_order_grid_data_source') {
            $select = $result->getSelect();
            $select->joinLeft(
                ["subuno_response"],
                'main_table.increment_id = subuno_response.transaction_id',
                ['action as subuno_response_action']
            );
        }

        return $result;
    }
}
