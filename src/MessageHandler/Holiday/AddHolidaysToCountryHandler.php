<?php

namespace App\MessageHandler\Holiday;

use App\Entity\Country;
use App\Factory\HolidayFactory;
use App\Message\Holiday\AddHolidaysToCountry;
use App\Model\Response\ApiClient\HolidayModel;
use App\Repository\HolidayRepository;
use App\Services\ApiRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddHolidaysToCountryHandler implements MessageHandlerInterface
{
    private string $kayaposoftBaseApiUrl;
    private ApiRequest $apiRequest;
    private HolidayFactory $holidayFactory;
    private EntityManagerInterface $entityManager;
    private HolidayRepository $holidayRepository;

    public function __construct(
        string $kayaposoftBaseApiUrl,
        ApiRequest $apiRequest,
        HolidayFactory $holidayFactory,
        HolidayRepository $holidayRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->kayaposoftBaseApiUrl = $kayaposoftBaseApiUrl;
        $this->apiRequest = $apiRequest;
        $this->holidayFactory = $holidayFactory;
        $this->entityManager = $entityManager;
        $this->holidayRepository = $holidayRepository;
    }

    public function __invoke(AddHolidaysToCountry $addHolidaysToCountry)
    {
        $holidayModels = $this->apiRequest->get(
            $this->getHolidayForYearUrl($addHolidaysToCountry->getYear(), $addHolidaysToCountry->getCountry()),
            'array<' . HolidayModel::class . '>'
        );
        foreach ($holidayModels as $holidayModel) {
            $this->createAndAssignHoliday($holidayModel, $addHolidaysToCountry->getCountry());
        }
    }

    private function createAndAssignHoliday(HolidayModel $holidayModel, Country $country): void
    {
        $holidayEntity = $this->holidayFactory->create($holidayModel);
        $holiday = $this->holidayRepository->findOneOrCreate($holidayEntity);
        $country->addHoliday($holiday);
        $this->entityManager->flush();
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
