<?php

namespace App\Services;

use App\Entity\Holiday;
use App\Factory\Model\HolidayRequestCheckDateModelFactory;
use App\Model\Request\Holiday\HolidayRequestDateModel;
use App\Model\Request\Holiday\HolidayRequestYearModel;
use App\Model\Response\KayaposoftApi\IsWorkDayModel;
use App\Request\KayaposoftApi\IsWorkDayRequest;
use Carbon\Carbon;
use DateTime;

class HolidayManager
{
    private const COUNT_FORWARD = 1;
    private const COUNT_BACKWARDS = 0;
    private HolidayRequestCheckDateModelFactory $checkDateModelFactory;
    private ApiClient $apiClient;

    public function __construct(
        HolidayRequestCheckDateModelFactory $checkDateModelFactory,
        ApiClient $apiClient
    ) {
        $this->checkDateModelFactory = $checkDateModelFactory;
        $this->apiClient = $apiClient;
    }

    /**
     * @param Holiday[] $holidays
     * @return int
     */
    public function getCountedFreeDays(array $holidays, HolidayRequestYearModel $holidayRequestForYearModel): int
    {
        $maxFreeDays = 0;
        $count = 0;
        $streakStartDate = null;
        $streakEndDate = null;
        $streakStarted = false;
        for ($holidayIndex = 1; $holidayIndex < count($holidays); $holidayIndex++) {
            $date0 = Carbon::parse($holidays[$holidayIndex - 1]->getDate());
            $date1 = Carbon::parse($holidays[$holidayIndex]->getDate());
            $dayDifference = $date0->diffInDays($date1);

            if ($dayDifference == 1) {
                if (!$streakStarted) {
                    $streakStartDate = $date0;
                    $streakStarted = true;
                }
                $count++;
            }

            if ($maxFreeDays < $count) {
                $maxFreeDays = $count;
            }

            if ($dayDifference != 1 || $holidayIndex == count($holidays) - 1) {
                $count++;
                if ($streakStarted) {
                    $streakEndDate = $dayDifference == 1 ? $date1 : $date0;
                    $streakStarted = false;
                }
                if ($maxFreeDays < $count) {
                    $maxFreeDays = $count;
                }
                $count = 0;
            }
        }

        return $this->countWeekend($holidayRequestForYearModel, $streakStartDate, $streakEndDate) + $maxFreeDays;
    }


    private function countWeekend(
        HolidayRequestYearModel $holidayRequestForYearModel,
        DateTime $streakStartDate,
        DateTime $streakEndDate
    ): int {
        $checkStartDateModel = $this->checkDateModelFactory->create(
            $holidayRequestForYearModel->getCountry(),
            $streakStartDate
        );

        $checkEndDateModel = $this->checkDateModelFactory->create(
            $holidayRequestForYearModel->getCountry(),
            $streakEndDate
        );

        return $this->abstractWeekendCounter($checkEndDateModel, 0, self::COUNT_FORWARD)
            + $this->abstractWeekendCounter($checkStartDateModel, 0, self::COUNT_BACKWARDS);
    }

    private function abstractWeekendCounter(
        HolidayRequestDateModel $checkDateModel,
        int $freeDays,
        int $direction
    ): int {
        /**@var IsWorkDayModel $isWorkDayModel**/
        $isWorkDayModel = $this->apiClient->request(new IsWorkDayRequest($checkDateModel));
        if ($isWorkDayModel->isWorkDay()) {
            return $freeDays;
        }
        $date = $checkDateModel->getDate();
        ($direction === self::COUNT_BACKWARDS)
            ? $checkDateModel->setDate(Carbon::parse($date)->subDay(1))
            : $checkDateModel->setDate(Carbon::parse($date)->addDay());

        /**@var IsWorkDayModel $isWorkDayModel**/
        $isWorkDayModel = $this->apiClient->request(new IsWorkDayRequest($checkDateModel));
        if (!$isWorkDayModel->isWorkDay()) {
            $freeDays = $this->abstractWeekendCounter($checkDateModel, $freeDays + 1, $direction);
        }
        return $freeDays;
    }
}
