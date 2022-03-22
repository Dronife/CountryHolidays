<?php

namespace App\Model\Error;

use OpenApi\Annotations as OA;

/**
 * Class FormErrorModel
 * @package App\Model\API\Error
 */
class FormErrorModel
{
    /**
     * @var string
     * @OA\Property(description="Form input name")
     */
    private string $field;

    /**
     * @var string
     */
    private string $message;

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function setField(string $field): self
    {
        $this->field = $field;

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
