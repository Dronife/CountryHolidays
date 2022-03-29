<?php

namespace App\Request\KayaposoftApi;

abstract class AbstractKaiaposoftApiRequest implements KayaposoftApiRequestInterface
{
    private string $url;
    private string $httpRequestType;
    private string $responseClass;
    private bool $deserializeArrayClassExists = false;
    private string $deserializeArrayToClass = "";

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getHttpRequestType(): string
    {
        return $this->httpRequestType;
    }

    /**
     * @param string $httpRequestType
     */
    public function setHttpRequestType(string $httpRequestType): void
    {
        $this->httpRequestType = $httpRequestType;
    }

    /**
     * @return string
     */
    public function getResponseClass(): string
    {
        return $this->responseClass;
    }

    /**
     * @param string $responseClass
     */
    public function setResponseClass(string $responseClass): void
    {
        $this->responseClass = $responseClass;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getDeserializeArrayToClass(): string
    {
        return $this->deserializeArrayToClass;
    }

    /**
     * @param string $deserializeArrayToClass
     */
    public function setDeserializeArrayToClass(string $deserializeArrayToClass): void
    {
        $this->deserializeArrayToClass = $deserializeArrayToClass;
    }

    /**
     * @return bool
     */
    public function doesDeserializeArrayClassExists(): bool
    {
        return $this->deserializeArrayClassExists;
    }

    /**
     * @param bool $deserializeArrayClassExists
     */
    public function setDeserializeArrayClassExists(bool $deserializeArrayClassExists): void
    {
        $this->deserializeArrayClassExists = $deserializeArrayClassExists;
    }
}
