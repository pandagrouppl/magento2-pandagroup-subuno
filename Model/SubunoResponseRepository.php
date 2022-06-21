<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Model;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use PandaGroup\Subuno\Api\Data\SubunoResponseInterface;
use PandaGroup\Subuno\Api\Data\SubunoResponseSearchResultInterface;
use PandaGroup\Subuno\Api\SubunoResponseRepositoryInterface;
use PandaGroup\Subuno\Model\ResourceModel\SubunoResponse as SubunoResponseResourceModel;
use PandaGroup\Subuno\Model\ResourceModel\SubunoResponse\CollectionFactory;
use PandaGroup\Subuno\Model\SubunoResponseFactory;
use Magento\Framework\Api\Search\SearchResult;
use Magento\Framework\Api\Search\SearchResultFactory;

class SubunoResponseRepository implements SubunoResponseRepositoryInterface
{
    private SubunoResponseResourceModel $subunoResponseResourceModel;
    private SubunoResponseFactory $subunoResponseFactory;
    private SearchResultFactory $searchResultFactory;
    private CollectionProcessorInterface $collectionProcessor;
    private CollectionFactory $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultFactory $searchResultFactory,
        SubunoResponseFactory $subunoResponseFactory,
        SubunoResponseResourceModel $subunoResponseResourceModel
    ) {
        $this->subunoResponseResourceModel = $subunoResponseResourceModel;
        $this->subunoResponseFactory = $subunoResponseFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->collectionFactory = $collectionFactory;
    }

    public function get(int $id): SubunoResponseInterface
    {
        $subunoResponse = $this->subunoResponseFactory->create();
        $this->subunoResponseResourceModel->load($subunoResponse, $id);

        return $subunoResponse;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResult
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $collection->load();
        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    public function save(SubunoResponseInterface $subunoResponse): void
    {
        $this->subunoResponseResourceModel->save($subunoResponse);
    }
}