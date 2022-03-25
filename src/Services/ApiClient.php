<?php

namespace App\Services;

use App\Model\Response\KayaposoftApi\KayaposoftApiModelInterface;
use App\Request\KayaposoftApi\AbstractKaiaposoftApiRequest;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{
    private SerializerInterface $serializer;
    private HttpClientInterface $client;

    public function __construct(SerializerInterface $serializer, HttpClientInterface $client)
    {
        $this->serializer = $serializer;
        $this->client = $client;
    }

    public function request(AbstractKaiaposoftApiRequest $kayaposoftApiRequest) : KayaposoftApiModelInterface
    {
        $requestType = $kayaposoftApiRequest->getHttpRequestType();
        $responseClass = $kayaposoftApiRequest->getResponseClass();
        $url = $kayaposoftApiRequest->getUrl();

        $response = $this->client->request($requestType, $url)->getContent();

        dump($kayaposoftApiRequest);
        dump($response);
        return $this->serializer->deserialize($response, $responseClass, 'json');
    }
}
