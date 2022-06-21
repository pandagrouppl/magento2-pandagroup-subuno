<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Api;

use Magento\Framework\Api\Search\SearchResult;
use Magento\Framework\Api\SearchCriteriaInterface;
use PandaGroup\Subuno\Api\Data\SubunoResponseInterface;

interface SubunoResponseRepositoryInterface
{
    /**
     * @param int $id
     * @return SubunoResponseInterface
     */
    public function get(int $id): SubunoResponseInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\Search\SearchResult
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResult;

    /**
     * @param SubunoResponseInterface $subunoResponse
     * @return void
     */
    public function save(SubunoResponseInterface $subunoResponse): void;
}