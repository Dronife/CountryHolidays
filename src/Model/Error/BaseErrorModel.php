<?php

namespace App\Model\Error;

class BaseErrorModel
{
    public const CODE_NOT_APPLICABLE = 'NotApplicable';
    public const CODE_NOT_FOUND = 'NotFound';
    public const CODE_BAD_REQUEST = 'BadRequest';

    /**
     * @var string
     */
    private string $code;

    /**
     * @var string
     */
    private string $message;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
