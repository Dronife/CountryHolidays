<?php

namespace App\Request\KayaposoftApi;

abstract class AbstractKaiaposoftApiRequest implements KayaposoftApiRequestInterface
{
    private string $url;
    private string $httpRequestType;
    private string $responseClass;
    private bool $objectConvertableToArrayVariable = false;
    private string $arrayObjectClass = "";

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
    public function getArrayObjectClass(): string
    {
        return $this->arrayObjectClass;
    }

    /**
     * @param string $arrayObjectClass
     */
    public function setArrayObjectClass(string $arrayObjectClass): void
    {
        $this->arrayObjectClass = $arrayObjectClass;
    }

    /**
     * @return bool
     */
    public function isObjectConvertableToArrayVariable(): bool
    {
        return $this->objectConvertableToArrayVariable;
    }

    /**
     * @param bool $objectConvertableToArrayVariable
     */
    public function setObjectConvertableToArrayVariable(bool $objectConvertableToArrayVariable): void
    {
        $this->objectConvertableToArrayVariable = $objectConvertableToArrayVariable;
    }
}
