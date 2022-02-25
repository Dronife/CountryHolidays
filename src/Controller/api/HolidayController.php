<?php

namespace App\Controller\api;

use App\Interfaces\HolidayHelperInterface;
use App\Model\Holiday\Holiday;
use App\Services\ModelConverterHelper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HolidayController extends AbstractFOSRestController
{

    private $holidayHelper;
    private $serializer;
    /**
     * @var ModelConverterHelper
     */
    private $converterHelper;

    public function __construct(HolidayHelperInterface $holidayHelper, SerializerInterface $serializer, ModelConverterHelper $converterHelper)
    {

        $this->holidayHelper = $holidayHelper;
        $this->serializer = $serializer;
        $this->converterHelper = $converterHelper;
    }

    /**
     * @Route("/api/holidays", methods={"get"})
     */
    public function holidays(Request $request): Response
    {
//        return $this->handleView($this->view($this->holidayHelper->getHolidaysByYearAndCountry(2022, 'Lithuania'), 200));
        return $this->handleView($this->view(
            $this->converterHelper->getHolidayModels(
                'GET',
                'https://kayaposoft.com/enrico/json/v2.0/?action=getHolidaysForYear&year=2022&country=ltu&holidayType=public_holiday',
            'array<'.Holiday::class.'>')
            , 200));
//        return new JsonResponse(
//            $this->serializer->serialize($this->holidayHelper->getHolidaysByYearAndCountry(2022, 'Angola'),
//                'json'),
//            200, [], true);
    }

}