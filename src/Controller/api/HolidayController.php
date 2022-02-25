<?php

namespace App\Controller\api;

use App\Interfaces\HolidayHelperInterface;
use App\Model\HolidayModel;
use App\Model\HolidayYearRequest;
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
     * @Route("/api/holidays", methods={"post"})
     */
    public function holidays(Request $request): Response
    {
        $requestModel = $this->converterHelper->getRequestModel($request->getContent(), HolidayYearRequest::class);
        dd($requestModel);
        return $this->handleView($this->view($this->holidayHelper->getHolidaysByYearAndCountry($request->get('year'), $request->get('country')), 200));
    }

}