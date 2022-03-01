<?php

namespace App\Controller\api;

use App\Form\Type\HolidayRequestCheckDateType;
use App\Form\Type\HolidayRequestForYearType;
use App\Interfaces\HolidayApiClientInterface;
use App\Model\HolidayModel;
use App\Model\HolidayRequestCheckDate;
use App\Model\HolidayRequestForYearModel;
use App\Services\ModelConverterHelper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HolidayController extends AbstractFOSRestController
{

    private $holidayApiClientService;
    private $serializer;
    /**
     * @var ModelConverterHelper
     */
    private $converterHelper;

    public function __construct(HolidayApiClientInterface $holidayApiClientService, SerializerInterface $serializer, ModelConverterHelper $converterHelper)
    {

        $this->holidayApiClientService = $holidayApiClientService;
        $this->serializer = $serializer;
        $this->converterHelper = $converterHelper;
    }

    /**
     * @Route("/api/holidays", methods={"post"})
     */
    public function holidays(Request $request): Response
    {
        $holidayRequestModel = new HolidayRequestForYearModel();
        $form = $this->createForm(HolidayRequestForYearType::class, $holidayRequestModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleView(
                $this->view(
                    $this->holidayApiClientService->getHolidaysByYearAndCountry(
                        $holidayRequestModel->getYear(), $holidayRequestModel->getCountry()),
                    200)
            );
        }
        return $this->handleView($this->view([$form->getErrors()]));
    }

    /**
     * @Route("/api/holidays/checkDate", methods={"post"})
     */
    public function checkDate(Request $request): Response
    {
        $holidayCheckDateModel = new HolidayRequestCheckDate();
        $form = $this->createForm(HolidayRequestCheckDateType::class, $holidayCheckDateModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleView(
                $this->view(
                    $this->holidayApiClientService->getDateHolidayType(
                        $holidayCheckDateModel->getDateByFormat('d-m-Y'), $holidayCheckDateModel->getCountry()),
                    200)
            );
//            return new JsonResponse([$holidayCheckDateModel->getDateByFormat('d-m-Y')]);
        }
        return $this->handleView($this->view([$form->getErrors()]));
    }

}