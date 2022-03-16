<?php

namespace App\Services;

use App\Entity\Holiday;
use Carbon\Carbon;

class HolidayManager
{
    /**
     * @param Holiday[] $holidays
     * @return int
     */
    public function getCountedFreeDays(array $holidays): int
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
        $continueToCountWeekendForward = true;
        $continueToCountWeekendBackward = true;
        for ($i = 0; $i < 2; $i++) {
            if ($streakStartDate->subDay(1)->isWeekend() && $continueToCountWeekendBackward) {
                $count++;
            } else {
                $continueToCountWeekendBackward = false;
            }

            if ($streakEndDate->addDay()->isWeekend() && $continueToCountWeekendForward) {
                $count++;
            } else {
                $continueToCountWeekendForward = false;
            }
        }

        return $count + $maxFreeDays;
    }
}
