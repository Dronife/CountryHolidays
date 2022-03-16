<?php

namespace App\MessageHandler\Holiday;

use App\Entity\Country;
use App\Factory\HolidayFactory;
use App\Message\Holiday\AddKayaposoftApiHolidaysToCountry;
use App\Message\Holiday\CreateAndAssignHoliday;
use App\Model\Response\ApiClient\HolidayModel;
use App\Repository\HolidayRepository;
use App\Services\ApiRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class AddKayaposoftApiHolidaysToCountryHandler implements MessageHandlerInterface
{
    private string $kayaposoftBaseApiUrl;
    private ApiRequest $apiRequest;
    private HolidayFactory $holidayFactory;
    private EntityManagerInterface $entityManager;
    private HolidayRepository $holidayRepository;
    private MessageBusInterface $messageBus;

    public function __construct(
        string $kayaposoftBaseApiUrl,
        ApiRequest $apiRequest,
        HolidayFactory $holidayFactory,
        HolidayRepository $holidayRepository,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus
    ) {
        $this->kayaposoftBaseApiUrl = $kayaposoftBaseApiUrl;
        $this->apiRequest = $apiRequest;
        $this->holidayFactory = $holidayFactory;
        $this->entityManager = $entityManager;
        $this->holidayRepository = $holidayRepository;
        $this->messageBus = $messageBus;
    }

    public function __invoke(AddKayaposoftApiHolidaysToCountry $addHolidaysToCountry)
    {
        $countryHasHolidays = $this->holidayRepository->countryHasHolidaysByYear(
            $addHolidaysToCountry->getYear(),
            $addHolidaysToCountry->getCountry()->getName()
        );
        if($countryHasHolidays)
        {
            return;
        }
        $holidayModels = $this->apiRequest->get(
            $this->getHolidayForYearUrl($addHolidaysToCountry->getYear(), $addHolidaysToCountry->getCountry()),
            'array<' . HolidayModel::class . '>'
        );
        foreach ($holidayModels as $holidayModel) {
            $this->messageBus->dispatch(new CreateAndAssignHoliday($holidayModel, $addHolidaysToCountry->getCountry()));
        }
    }

    private function getHolidayForYearUrl($year, Country $country): string
    {
        return sprintf(
            '%sgetHolidaysForYear&year=%s&country=%s&holidayType=public_holiday',
            $this->kayaposoftBaseApiUrl,
            $year,
            $country->getCountryCode()
        );
    }
}
