<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Service;

use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\Data\OrderInterface;
use PandaGroup\Subuno\Api\Data\SubunoResponseInterface;
use PandaGroup\Subuno\Api\TransformerInterface;
use PandaGroup\Subuno\Model\SubunoResponseFactory;
use PandaGroup\SubunoApi\Client;
use PandaGroup\SubunoApi\Exception\DataObjectException;
use PandaGroup\SubunoApi\Request\Builder\QueryBuilder;
use Psr\Log\LoggerInterface;
use Throwable;

class Connector
{
    /**
     * @var TransformerInterface[]
     */
    private array $transformers;
    private Client $client;
    private LoggerInterface $logger;
    private SubunoResponseFactory $subunoResponseFactory;
    private Json $json;

    public function __construct(
        Client $client,
        LoggerInterface $logger,
        Json $json,
        SubunoResponseFactory $subunoResponseFactory,
        array $transformers = []
    ) {
        $this->client = $client;
        $this->transformers = $transformers;
        $this->logger = $logger;
        $this->subunoResponseFactory = $subunoResponseFactory;
        $this->json = $json;
    }

    public function execute(OrderInterface $order): ?SubunoResponseInterface
    {
        $queryBuilder = new QueryBuilder();
        foreach ($this->transformers as $transformer) {
            try {
                $queryBuilder->add($transformer->transform($order));
            } catch (DataObjectException $exception) {
                $this->logWarning($exception);
                continue;
            }
        }

        try {
            $response = $this->client->execute($queryBuilder->build());
            $data = $response->getBody()->getContents();
            $decoded = $this->json->unserialize($data);
            /** @var SubunoResponseInterface $subunoResponse */
            $subunoResponse = $this->subunoResponseFactory->create();
            return $subunoResponse
                ->setRawResponse($data)
                ->setAction($decoded['action'])
                ->setReferenceCode($decoded['ref_code'])
                ->setTransactionId($order->getIncrementId());
        } catch (Throwable $exception) {
            $this->logWarning($exception);
            return null;
        }
    }

    private function logWarning(Throwable $exception): void
    {
        $this->logger->warning(get_class($exception) . ': ' . $exception->getMessage());
    }
}