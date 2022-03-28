<?php

namespace App\Request\KayaposoftApi;

use App\Constants\DateFormat;
use App\Entity\Country;
use App\Model\Request\Holiday\HolidayRequestCheckDateModel;
use App\Model\Response\KayaposoftApi\HolidayDateRangeModel;
use App\Model\Response\KayaposoftApi\HolidayModel;

class HolidayForDateRangeRequest extends AbstractKaiaposoftApiRequest
{
    public function __construct(HolidayRequestCheckDateModel $holidayRequestCheckDateModel)
    {
        parent::__construct();
        //TODO: Cia blogai, nes turi buti start ir end dates
        $date = $holidayRequestCheckDateModel->getDateByFormat(DateFormat::DATE_FORMAT_HOLIDAY_CHECK_DATE);
        $this->setResponseClass(HolidayDateRangeModel::class);
        $this->setDeserializeArrayClassExists(true);
        $this->setDeserializeArrayToClass('array<'.HolidayModel::class.'>');
        $this->setHttpRequestType('GET');
        $this->setUrl(
            sprintf(
                '%sgetHolidaysForDateRange&fromDate=%s&toDate=%s&country=%s',
                $_ENV['BASE_API_URL'],
                $date,
                $date,
                $holidayRequestCheckDateModel->getCountry()->getCountryCode()
            )
        );

    }

}
