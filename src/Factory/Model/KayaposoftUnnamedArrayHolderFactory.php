<?php

namespace App\Factory\Model;

use App\Model\Response\KayaposoftApi\AbstractArrayModel;
use App\Model\Response\KayaposoftApi\KayaposoftApiModelInterface;

class KayaposoftUnnamedArrayHolderFactory
{
    public function create(string $classToCreate, array $data) : KayaposoftApiModelInterface
    {
        $modelWithUnnamedArray = new $classToCreate();
        $modelWithUnnamedArray->setArray($data);
        return $modelWithUnnamedArray;
    }
}
