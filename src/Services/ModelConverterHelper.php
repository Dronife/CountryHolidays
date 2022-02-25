<?php

namespace App\Services;

use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ModelConverterHelper
{

    private $serializer;
    private $client;

    public function __construct(SerializerInterface $serializer, HttpClientInterface $client)
    {
        $this->serializer = $serializer;
        $this->client = $client;
    }

    public function getEntityModel($method, $url, $class) : array
    {
        $response = $this->client->request($method, $url)->getContent();
        return $this->serializer->deserialize($response, $class, 'json');
    }

    public function getRequestModel(string $requestContent, $class){
        return $this->serializer->deserialize($requestContent, $class, 'json');
    }
}