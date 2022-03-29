<?php

namespace App\Factory\Error;

use App\Model\Error\FormErrorModel;

/**
 * Class FormErrorModelFactory
 * @package App\Factory\API\Error
 */
class FormErrorModelFactory
{
    /**
     * @param string $field
     * @param string $message
     * @return FormErrorModel
     */
    public function create(string $field, string $message): FormErrorModel
    {
        return (new FormErrorModel())->setField($field)->setMessage($message);
    }
}
