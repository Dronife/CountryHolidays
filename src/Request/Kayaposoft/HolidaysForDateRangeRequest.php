<?php
declare(strict_types=1);
namespace App\Request\Kayaposoft;

use App\Constants\DateFormat;
use App\Entity\Country;
use App\Model\Request\Holiday\HolidayRequestCheckDate;
use App\Model\Request\Holiday\HolidayRequestInterface;
use App\Model\Response\KayaposoftApi\HolidayModel;
use App\Model\Response\KayaposoftApi\KayaposoftApiModelInterface;
use App\Services\ApiRequest;

class HolidaysForDateRangeRequest implements KayaposoftApiRequestInterface
{
    private string $kayaposoftBaseApiUrl;
    private ApiRequest $apiRequest;

    public function __construct(string $kayaposoftBaseApiUrl, ApiRequest $apiRequest)
    {
        $this->kayaposoftBaseApiUrl = $kayaposoftBaseApiUrl;
        $this->apiRequest = $apiRequest;
    }

    /**
     * @param HolidayRequestCheckDate $holidayRequest
     * @return HolidayModel
     **/
    public function getModel(HolidayRequestInterface $holidayRequest): KayaposoftApiModelInterface
    {
        $country = $holidayRequest->getCountry();
        $date = $holidayRequest->getDateByFormat(DateFormat::DATE_FORMAT_HOLIDAY_CHECK_DATE);

        return $this->apiRequest
            ->get($this->getUrlForSpecificHolidayDate($country, $date), 'array<' . HolidayModel::class . '>')[0];
    }

    public function getUrlForSpecificHolidayDate(Country $country, string $date): string
    {
        return sprintf(
            '%sgetHolidaysForDateRange&fromDate=%s&toDate=%s&country=%s',
            $this->kayaposoftBaseApiUrl,
            $date,
            $date,
            $country->getCountryCode()
        );
    }
}
