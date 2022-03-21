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
    public function __construct()
    {
    }

    public function isFreeDay(string $date): bool
    {
        return Carbon::parse($date)->isWeekend();
    }
}
