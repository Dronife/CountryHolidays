<?php

namespace App\Services;

use App\Factory\Model\KayaposoftModelWithUnnamedArrayFactory;
use App\Model\Response\KayaposoftApi\AbstractArrayModel;
use App\Model\Response\KayaposoftApi\KayaposoftApiModelInterface;
use App\Request\KayaposoftApi\AbstractKaiaposoftApiRequest;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{
    private SerializerInterface $serializer;
    private HttpClientInterface $client;
    private KayaposoftModelWithUnnamedArrayFactory $kayaposoftModelWithUnnamedArrayFactory;

    public function __construct(
        SerializerInterface $serializer,
        HttpClientInterface $client,
        KayaposoftModelWithUnnamedArrayFactory $kayaposoftModelWithUnnamedArrayFactory
    ) {
        $this->serializer = $serializer;
        $this->client = $client;
        $this->kayaposoftModelWithUnnamedArrayFactory = $kayaposoftModelWithUnnamedArrayFactory;
    }

    public function request(AbstractKaiaposoftApiRequest $kayaposoftApiRequest): ?KayaposoftApiModelInterface
    {
        $responseClass = $kayaposoftApiRequest->getResponseClass();

        $deserializeToClass = $kayaposoftApiRequest->isObjectConvertableToArrayVariable()
            ? $kayaposoftApiRequest->getArrayObjectClass()
            : $responseClass;

        $requestResponse = $this->client->request(
            $kayaposoftApiRequest->getHttpRequestType(),
            $kayaposoftApiRequest->getUrl()
        )->getContent();

        $deserialized = $this->serializer->deserialize($requestResponse, $deserializeToClass, 'json');

        return $kayaposoftApiRequest->isObjectConvertableToArrayVariable()
            ? $this->kayaposoftModelWithUnnamedArrayFactory->create($responseClass, $deserialized)
            : $deserialized;
    }
}
