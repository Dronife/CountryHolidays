<?php

namespace App\Services\LogicHandlers\Holiday;

use App\Constants\DateFormat;
use App\Constants\DateType;
use App\Factory\Model\HolidayWithDateRangeFactory;
use App\Message\Holiday\CreateAndAssignHoliday;
use App\Model\Request\Holiday\HolidayRequestDateModel;
use App\Model\Response\KayaposoftApi\HolidayDateRangeModel;
use App\Model\Response\KayaposoftApi\HolidayModel;
use App\Model\Response\KayaposoftApi\IsPublicHolidayModel;
use App\Model\Response\KayaposoftApi\IsWorkDayModel;
use App\Request\KayaposoftApi\HolidayForDateRangeRequest;
use App\Request\KayaposoftApi\IsPublicHolidayRequest;
use App\Request\KayaposoftApi\IsWorkDayRequest;
use App\Services\ApiClient;
use Symfony\Component\Messenger\MessageBusInterface;

class HolidayControllerLogicHandler
{
    private MessageBusInterface $messageBus;
    private ApiClient $apiClient;
    private HolidayWithDateRangeFactory $holidayWithDateRangeFactory;

    public function __construct(
        MessageBusInterface $messageBus,
        ApiClient $apiClient,
        HolidayWithDateRangeFactory $holidayWithDateRangeFactory
    ) {
        $this->messageBus = $messageBus;
        $this->apiClient = $apiClient;
        $this->holidayWithDateRangeFactory = $holidayWithDateRangeFactory;
    }

    public function getDateTypeAndSaveHoliday(HolidayRequestDateModel $holidayCheckDateModel): string
    {
        $date = $holidayCheckDateModel->getDateByFormat(DateFormat::DATE_FORMAT_HOLIDAY_CHECK_DATE);
        $country = $holidayCheckDateModel->getCountry();

        if ($country->getHolidayByDate($date) !== null) {
            return DateType::TYPE_HOLIDAY;
        }

        /** @var IsPublicHolidayModel $isPublicHoliday */
        $isPublicHoliday = $this->apiClient->request(new IsPublicHolidayRequest($country, $date));
        if (!$isPublicHoliday->isPublicHoliday()) {

            /**
             * @var IsWorkDayModel $isPublicHoliday
             */
            $isPublicHoliday = $this->apiClient->request(new IsWorkDayRequest($holidayCheckDateModel));
            if (!$isPublicHoliday->isWorkDay()) {
                return DateType::TYPE_FREE_DAY;
            }
            return DateType::TYPE_WORKDAY;
        }

        $holidayDateWithRange = $this->holidayWithDateRangeFactory->create($holidayCheckDateModel);

        /**
         * @var HolidayDateRangeModel $holidays
         */
        $holidays = $this->apiClient->request(new HolidayForDateRangeRequest($holidayDateWithRange));
        $this->messageBus->dispatch(
            new CreateAndAssignHoliday(
                $holidays->getFirst(),
                $country
            )
        );

        return DateType::TYPE_HOLIDAY;
    }

}
