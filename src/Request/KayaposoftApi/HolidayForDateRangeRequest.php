<?php

namespace App\Request\KayaposoftApi;

use App\Constants\DateFormat;
use App\Model\Request\Holiday\HolidayWithDateRangeModel;
use App\Model\Response\KayaposoftApi\HolidayDateRangeModel;
use App\Model\Response\KayaposoftApi\HolidayModel;

class HolidayForDateRangeRequest extends AbstractKaiaposoftApiRequest
{
    public function __construct(HolidayWithDateRangeModel $holidayDateRangeModel)
    {
        parent::__construct();
        $this->setResponseClass(HolidayDateRangeModel::class);
        $this->setDeserializeArrayClassExists(true);
        $this->setDeserializeArrayToClass('array<'.HolidayModel::class.'>');
        $this->setHttpRequestType('GET');
        $this->setUrl(
            sprintf(
                '%sgetHolidaysForDateRange&fromDate=%s&toDate=%s&country=%s',
                $_ENV['BASE_API_URL'],
                $holidayDateRangeModel->getStartDateWithFormat(DateFormat::DATE_FORMAT_HOLIDAY_CHECK_DATE),
                $holidayDateRangeModel->getEndDateWithFormat(DateFormat::DATE_FORMAT_HOLIDAY_CHECK_DATE),
                $holidayDateRangeModel->getCountry()->getCountryCode()
            )
        );

    }

}
