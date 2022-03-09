<?php

namespace App\Services;

use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiRequest
{
    private $serializer;
    private $client;

    public function __construct(SerializerInterface $serializer, HttpClientInterface $client)
    {
        $this->serializer = $serializer;
        $this->client = $client;
    }

//    public function getModelArray($method, $url, $class) : array
//    {
//        $response = $this->client->request($method, $url)->getContent();
//        return $this->serializer->deserialize($response, 'array<'.$class.'>', 'json');
//    }

    public function get($url, $class)
    {
        $response = $this->client->request('GET', $url)->getContent();
        return $this->serializer->deserialize($response, $class, 'json');
    }

    public function getRequestModel(string $requestContent, $class)
    {
        return $this->serializer->deserialize($requestContent, $class, 'json');
    }
}
