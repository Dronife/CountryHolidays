<?php

namespace App\MessageHandler\Holiday;

use App\Factory\HolidayFactory;
use App\Message\Holiday\CreateAndAssignHoliday;
use App\Repository\HolidayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateAndAssignHolidayHandler implements MessageHandlerInterface
{
    private HolidayFactory $holidayFactory;
    private HolidayRepository $holidayRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        HolidayFactory $holidayFactory,
        HolidayRepository $holidayRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->holidayFactory = $holidayFactory;
        $this->holidayRepository = $holidayRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(CreateAndAssignHoliday $createAndAssignHoliday)
    {
        $holidayEntity = $this->holidayFactory->create($createAndAssignHoliday->getHolidayModel());
        $holiday = $this->holidayRepository->findOneOrCreate($holidayEntity);
        $createAndAssignHoliday->getCountry()->addHoliday($holiday);
        $this->entityManager->flush();
    }
}
