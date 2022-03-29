<?php

namespace App\Model\Response\KayaposoftApi;

abstract class AbstractArrayModel implements KayaposoftApiModelInterface
{
    private array $objectColection;

    public function __construct()
    {
    }


    abstract public function setArray(array $objectCollection);

}
