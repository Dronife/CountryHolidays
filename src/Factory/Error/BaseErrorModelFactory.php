<?php

namespace App\Factory\Error;

use App\Model\Error\BaseErrorModel;

/**
 * Class BaseErrorModelFactory
 * @package App\Factory\API\Error
 */
class BaseErrorModelFactory
{
    /**
     * @param string $message
     * @param string $code
     * @return BaseErrorModel
     */
    public function create(string $message, string $code): BaseErrorModel
    {
        return (new BaseErrorModel())->setCode($code)->setMessage($message);
    }
}

