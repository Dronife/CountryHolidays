<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Country;
use App\Entity\Holiday;
use App\Factory\HolidayFactory;
use App\Interfaces\CountryApiClientInterface;
use App\Interfaces\HolidayApiClientInterface;
use App\Model\Response\KayaposoftApi\IsPublicHolidayModel;
use App\Model\Response\KayaposoftApi\HolidayModel;
use App\Repository\CountryRepository;
use App\Repository\HolidayRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HolidayApiClientService
{

    private string $kayaposoftBaseApiUrl;
    private ApiRequest $apiRequest;

    public function __construct(
        string $kayaposoftBaseApiUrl,
        ApiRequest $apiRequest
    ) {
        $this->kayaposoftBaseApiUrl = $kayaposoftBaseApiUrl;
        $this->apiRequest = $apiRequest;
    }


//    public function getOneHolidayModel(Country $country, string $date): HolidayModel
//    {
//        return $this->apiRequest
//            ->get($this->getUrlForSpecificHolidayDate($country, $date), 'array<' . HolidayModel::class . '>')[0];
//    }


    public function isFreeDay(string $date): bool
    {
        return Carbon::parse($date)->isWeekend();
    }

    private function getUrlForDayCheck(Country $country, string $date): string
    {
        return sprintf(
            '%sisPublicHoliday&date=%s&country=%s',
            $this->kayaposoftBaseApiUrl,
            $date,
            $country->getCountryCode()
        );
    }

//    public function getUrlForSpecificHolidayDate(Country $country, string $date): string
//    {
//        return sprintf(
//            '%sgetHolidaysForDateRange&fromDate=%s&toDate=%s&country=%s',
//            $this->kayaposoftBaseApiUrl,
//            $date,
//            $date,
//            $country->getCountryCode()
//        );
//    }

    public function isPublicHoliday(Country $country, string $date): bool
    {
        return $this->apiRequest
            ->get($this->getUrlForDayCheck($country, $date), IsPublicHolidayModel::class)
            ->isPublicHoliday();
    }

}
