<?php

namespace App\Services;

use App\Factory\Model\KayaposoftUnnamedArrayHolderFactory;
use App\Model\Response\KayaposoftApi\AbstractArrayModel;
use App\Model\Response\KayaposoftApi\KayaposoftApiModelInterface;
use App\Request\KayaposoftApi\AbstractKaiaposoftApiRequest;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{
    private SerializerInterface $serializer;
    private HttpClientInterface $client;
    private KayaposoftUnnamedArrayHolderFactory $kayaposoftUnnamedArrayHolderFactory;

    public function __construct(
        SerializerInterface $serializer,
        HttpClientInterface $client,
        KayaposoftUnnamedArrayHolderFactory $kayaposoftUnnamedArrayHolderFactory
    ) {
        $this->serializer = $serializer;
        $this->client = $client;
        $this->kayaposoftUnnamedArrayHolderFactory = $kayaposoftUnnamedArrayHolderFactory;
    }

    public function request(AbstractKaiaposoftApiRequest $kayaposoftApiRequest): ?KayaposoftApiModelInterface
    {
        $responseClass = $kayaposoftApiRequest->getResponseClass();

        $deserializeToClass = $kayaposoftApiRequest->doesDeserializeArrayClassExists()
            ? $kayaposoftApiRequest->getDeserializeArrayToClass()
            : $responseClass;

        $requestResponse = $this->client->request(
            $kayaposoftApiRequest->getHttpRequestType(),
            $kayaposoftApiRequest->getUrl()
        )->getContent();

        $deserialized = $this->serializer->deserialize($requestResponse, $deserializeToClass, 'json');

        return $kayaposoftApiRequest->doesDeserializeArrayClassExists()
            ? $this->kayaposoftUnnamedArrayHolderFactory->create($responseClass, $deserialized)
            : $deserialized;
    }
}
