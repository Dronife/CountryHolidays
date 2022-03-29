<?php

namespace App\Request\KayaposoftApi;

use App\Constants\DateFormat;
use App\Entity\Country;
use App\Model\Request\Holiday\HolidayRequestDateModel;
use App\Model\Response\KayaposoftApi\IsWorkDayModel;

class IsWorkDayRequest extends AbstractKaiaposoftApiRequest
{
    public function __construct(HolidayRequestDateModel $holidayRequestCheckDateModel)
    {
        parent::__construct();
        $this->setResponseClass(IsWorkDayModel::class);
        $this->setHttpRequestType('GET');
        $this->setUrl(
            sprintf(
                '%sisWorkDay&date=%s&country=%s',
                $_ENV['BASE_API_URL'],
                $holidayRequestCheckDateModel->getDateByFormat(DateFormat::DATE_FORMAT_HOLIDAY_CHECK_DATE),
                $holidayRequestCheckDateModel->getCountry()->getCountryCode()
            )
        );
    }
}
