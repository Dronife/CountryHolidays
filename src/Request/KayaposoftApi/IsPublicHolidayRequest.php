<?php

namespace App\Request\KayaposoftApi;

use App\Entity\Country;
use App\Model\Response\KayaposoftApi\HolidayModel;
use App\Model\Response\KayaposoftApi\IsPublicHolidayModel;

class IsPublicHolidayRequest extends AbstractKaiaposoftApiRequest
{
    public function __construct(Country $country, string $date)
    {
        parent::__construct();
        $this->setResponseClass(IsPublicHolidayModel::class);
        $this->setHttpRequestType('GET');
        $this->setUrl(
            sprintf(
                '%sisPublicHoliday&date=%s&country=%s',
                $_ENV['BASE_API_URL'],
                $date,
                $country->getCountryCode()
            )
        );
    }
}
