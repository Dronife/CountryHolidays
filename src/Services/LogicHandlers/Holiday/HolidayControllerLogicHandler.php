<?php

namespace App\Services\LogicHandlers\Holiday;

use App\Constants\DateFormat;
use App\Constants\DateType;
use App\Factory\KayaposoftApi\KayaposoftRequestFactory;
use App\Message\Holiday\CreateAndAssignHoliday;
use App\Model\Request\Holiday\HolidayRequestCheckDate;
use App\Request\Kayaposoft\HolidaysForDateRangeRequest;
use App\Request\Kayaposoft\IsPublicHolidayRequest;
use App\Services\HolidayApiClientService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class HolidayControllerLogicHandler
{
    private HolidayApiClientService $holidayApiClientService;
    private MessageBusInterface $messageBus;
    private KayaposoftRequestFactory $kayaposoftRequestFactory;
    private HolidaysForDateRangeRequest $holidaysForDateRangeRequest;
    private IsPublicHolidayRequest $isPublicHolidayRequest;

    public function __construct(
        HolidayApiClientService $holidayApiClientService,
        MessageBusInterface $messageBus,
        KayaposoftRequestFactory $kayaposoftRequestFactory,
        HolidaysForDateRangeRequest $holidaysForDateRangeRequest,
        IsPublicHolidayRequest $isPublicHolidayRequest
    ) {
        $this->holidayApiClientService = $holidayApiClientService;
        $this->messageBus = $messageBus;
        $this->kayaposoftRequestFactory = $kayaposoftRequestFactory;
        $this->holidaysForDateRangeRequest = $holidaysForDateRangeRequest;
        $this->isPublicHolidayRequest = $isPublicHolidayRequest;
    }

    public function getDateTypeAndSaveHoliday(HolidayRequestCheckDate $holidayCheckDateModel): string
    {
        $date = $holidayCheckDateModel->getDateByFormat(DateFormat::DATE_FORMAT_HOLIDAY_CHECK_DATE);
        $country = $holidayCheckDateModel->getCountry();

        if ($country->getHolidayByDate($date) !== null) {
            return DateType::TYPE_HOLIDAY;
        }
        $isDatePublicHoliday = $this->kayaposoftRequestFactory->handleRequest(
            $this->isPublicHolidayRequest,
            $holidayCheckDateModel
        )->isPublicHoliday();

        if (!$isDatePublicHoliday) {
            if ($this->holidayApiClientService->isFreeDay($date)) {
                return DateType::TYPE_FREE_DAY;
            }
            return DateType::TYPE_WORKDAY;
        }

        $holidayModel = $this->kayaposoftRequestFactory->handleRequest(
            $this->holidaysForDateRangeRequest,
            $holidayCheckDateModel
        );

        $this->messageBus->dispatch(
            new CreateAndAssignHoliday(
                $holidayModel,
                $country
            )
        );

        return DateType::TYPE_HOLIDAY;
    }

}
