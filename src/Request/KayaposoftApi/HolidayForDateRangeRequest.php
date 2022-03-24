<?php

namespace App\Request\KayaposoftApi;

use App\Entity\Country;
use App\Model\Response\KayaposoftApi\HolidayModel;

class HolidayForDateRangeRequest extends AbstractKaiaposoftApiRequest
{
    public function __construct(string $startDate,string $endDate, Country $country)
    {
        parent::__construct();
        $this->setResponseClass('array<' . HolidayModel::class . '>');
        $this->setHttpRequestType('GET');
        $this->setUrl(
            sprintf(
                '%sgetHolidaysForDateRange&fromDate=%s&toDate=%s&country=%s',
                $_ENV['BASE_API_URL'],
                $startDate,
                $endDate,
                $country->getCountryCode()
            )
        );

    }

}
