<?php

namespace App\Request\Kayaposoft;

use App\Model\Request\Holiday\HolidayRequestInterface;
use App\Model\Response\KayaposoftApi\KayaposoftApiModelInterface;

interface KayaposoftApiRequestInterface
{
    public function getModel(HolidayRequestInterface $holidayRequest) : KayaposoftApiModelInterface ;

}
