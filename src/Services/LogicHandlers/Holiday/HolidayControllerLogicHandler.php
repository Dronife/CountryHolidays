<?php

namespace App\Services\LogicHandlers\Holiday;

use App\Constants\DateFormat;
use App\Constants\DateType;
use App\Message\Holiday\CreateAndAssignHoliday;
use App\Model\Request\Holiday\HolidayRequestCheckDateModel;
use App\Model\Response\KayaposoftApi\HolidayModel;
use App\Model\Response\KayaposoftApi\IsPublicHolidayModel;
use App\Model\Response\KayaposoftApi\IsWorkDayModel;
use App\Request\KayaposoftApi\HolidayForDateRangeRequest;
use App\Request\KayaposoftApi\IsPublicHolidayRequest;
use App\Request\KayaposoftApi\IsWorkDayRequest;
use App\Services\ApiClient;
use App\Strategy\KayaposoftApiRequestStrategy;
use Symfony\Component\Messenger\MessageBusInterface;

class HolidayControllerLogicHandler
{
    private MessageBusInterface $messageBus;
    private KayaposoftApiRequestStrategy $apiRequestStrategy;
    private ApiClient $apiClient;

    public function __construct(
        MessageBusInterface $messageBus,
        KayaposoftApiRequestStrategy $apiRequestStrategy,
        ApiClient $apiClient
    ) {
        $this->messageBus = $messageBus;
        $this->apiRequestStrategy = $apiRequestStrategy;
        $this->apiClient = $apiClient;
    }

    public function getDateTypeAndSaveHoliday(HolidayRequestCheckDateModel $holidayCheckDateModel): string
    {
        $date = $holidayCheckDateModel->getDateByFormat(DateFormat::DATE_FORMAT_HOLIDAY_CHECK_DATE);
        $country = $holidayCheckDateModel->getCountry();

        if ($country->getHolidayByDate($date) !== null) {
            return DateType::TYPE_HOLIDAY;
        }

        /**
         * @var IsPublicHolidayModel $isPublicHoliday
         */
        $isPublicHoliday = $this->apiClient->request(new IsPublicHolidayRequest($country, $date));
        if (!$isPublicHoliday->isPublicHoliday()) {

            /**
             * @var IsWorkDayModel $isPublicHoliday
             */
            $isPublicHoliday = $this->apiClient->request(new IsWorkDayRequest($country, $date));
            if (!$isPublicHoliday->isWorkDay()) {
                return DateType::TYPE_FREE_DAY;
            }
            return DateType::TYPE_WORKDAY;
        }

        /**
         * @var HolidayModel $holiday
         */
        $holiday = $this->apiClient->arrayRequest(new HolidayForDateRangeRequest($date, $date, $country))[0];
        $this->messageBus->dispatch(
            new CreateAndAssignHoliday(
                $holiday,
                $country
            )
        );

        return DateType::TYPE_HOLIDAY;
    }

}
