<?php

namespace App\Services\LogicHandlers\Holiday;

use App\Constants\DateFormat;
use App\Constants\DateType;
use App\Message\Holiday\CreateAndAssignHoliday;
use App\Model\Request\Holiday\HolidayRequestCheckDate;
use App\Services\HolidayApiClientService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class HolidayControllerLogicHandler
{
    private HolidayApiClientService $holidayApiClientService;
    private MessageBusInterface $messageBus;

    public function __construct(HolidayApiClientService $holidayApiClientService, MessageBusInterface $messageBus)
    {
        $this->holidayApiClientService = $holidayApiClientService;
        $this->messageBus = $messageBus;
    }

    public function getDateTypeAndSaveHoliday(HolidayRequestCheckDate $holidayCheckDateModel) : string
    {
        $date = $holidayCheckDateModel->getDateByFormat(DateFormat::DATE_FORMAT_HOLIDAY_CHECK_DATE);
        $country = $holidayCheckDateModel->getCountry();

        if ($country->getHolidayByDate($date) !== null) {
            return DateType::TYPE_HOLIDAY;
        }

        if (!$this->holidayApiClientService->isPublicHoliday($country, $date)) {
            if ($this->holidayApiClientService->isFreeDay($date)) {
                return DateType::TYPE_FREE_DAY;
            }
            return DateType::TYPE_WORKDAY;
        }

        $this->messageBus->dispatch(
            new CreateAndAssignHoliday(
                $this->holidayApiClientService->getOneHolidayModel($country, $date),
                $country
            )
        );

        return DateType::TYPE_HOLIDAY;
    }

}
