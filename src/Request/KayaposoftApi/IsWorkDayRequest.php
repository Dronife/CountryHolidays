<?php

namespace App\Request\KayaposoftApi;

use App\Entity\Country;
use App\Model\Response\KayaposoftApi\IsWorkDayModel;

class IsWorkDayRequest extends AbstractKaiaposoftApiRequest
{
    public function __construct(Country $country, string $date)
    {
        parent::__construct();
        $this->setResponseClass(IsWorkDayModel::class);
        $this->setHttpRequestType('GET');
        $this->setUrl(
            sprintf(
                '%sisWorkDay&date=%s&country=%s',
                $_ENV['BASE_API_URL'],
                $date,
                $country->getCountryCode()
            )
        );
    }
}
