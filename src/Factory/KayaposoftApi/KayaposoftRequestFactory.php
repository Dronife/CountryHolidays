<?php

namespace App\Factory\KayaposoftApi;

use App\Model\Request\Holiday\HolidayRequestInterface;
use App\Model\Response\KayaposoftApi\KayaposoftApiModelInterface;
use App\Request\Kayaposoft\KayaposoftApiRequestInterface;

class KayaposoftRequestFactory
{
    public function handleRequest(
        KayaposoftApiRequestInterface $kayaposoftApiRequest,
        HolidayRequestInterface $holidayRequest
    ): KayaposoftApiModelInterface {
        return $kayaposoftApiRequest->getModel($holidayRequest);
    }
}
