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
        return $this->serializer->deserialize($this->getResponse($kayaposoftApiRequest), $kayaposoftApiRequest->getResponseClass(), 'json');
    }

    /**
     * @return KayaposoftApiModelInterface[]
     */
    public function arrayRequest(AbstractKaiaposoftApiRequest $kayaposoftApiRequest) : array
    {
        return $this->serializer->deserialize($this->getResponse($kayaposoftApiRequest), $kayaposoftApiRequest->getResponseClass(), 'json');
    }

    private function getResponse(AbstractKaiaposoftApiRequest $kayaposoftApiRequest) : string
    {
        $requestType = $kayaposoftApiRequest->getHttpRequestType();
        $url = $kayaposoftApiRequest->getUrl();

        return $this->client->request($requestType, $url)->getContent();
    }
}
