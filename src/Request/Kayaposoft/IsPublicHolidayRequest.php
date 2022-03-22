<?php

namespace App\Request\Kayaposoft;

use App\Constants\DateFormat;
use App\Entity\Country;
use App\Model\Request\Holiday\HolidayRequestCheckDateModel;
use App\Model\Request\Holiday\HolidayRequestInterface;
use App\Model\Response\KayaposoftApi\IsPublicHolidayModel;
use App\Model\Response\KayaposoftApi\KayaposoftApiModelInterface;
use App\Services\ApiRequest;

class IsPublicHolidayRequest implements KayaposoftApiRequestInterface
{
    private string $kayaposoftBaseApiUrl;
    private ApiRequest $apiRequest;

    public function __construct(string $kayaposoftBaseApiUrl, ApiRequest $apiRequest)
    {
        $this->kayaposoftBaseApiUrl = $kayaposoftBaseApiUrl;
        $this->apiRequest = $apiRequest;
    }

    /**
     * @param HolidayRequestCheckDateModel $holidayRequest
     * @return IsPublicHolidayModel
     **/
    public function getModel(HolidayRequestInterface $holidayRequest): KayaposoftApiModelInterface
    {
        $country = $holidayRequest->getCountry();
        $date = $holidayRequest->getDateByFormat(DateFormat::DATE_FORMAT_HOLIDAY_CHECK_DATE);

        return $this->apiRequest
            ->get($this->getUrlIsPublicHoliday($country, $date), IsPublicHolidayModel::class);
    }

    private function getUrlIsPublicHoliday(Country $country, string $date): string
    {
        return sprintf(
            '%sisPublicHoliday&date=%s&country=%s',
            $this->kayaposoftBaseApiUrl,
            $date,
            $country->getCountryCode()
        );
    }
}
