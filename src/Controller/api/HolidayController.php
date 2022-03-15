<?php

namespace App\Controller\api;

use App\Form\Type\HolidayRequestCheckDateType;
use App\Form\Type\HolidayRequestForYearType;
use App\Interfaces\HolidayApiClientInterface;
use App\Model\Request\Holiday\HolidayRequestCheckDate;
use App\Model\Request\Holiday\HolidayRequestForYearModel;
use App\Model\Response\Holiday\HolidayResponseForYearModel;
use App\Services\ApiRequest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/holidays")
 * 
 * @OA\Response(
 *     response=400,
 *     description="Request data incorrect",
 * )
 * @OA\Response(
 *     response=500,
 *     description="Unexpected server error",
 * )
 */
class HolidayController extends AbstractFOSRestController
{
    private HolidayApiClientInterface $holidayApiClientService;
    private SerializerInterface $serializer;
    /**
     * @var ApiRequest
     */
    private ApiRequest $converterHelper;

    public function __construct(
        HolidayApiClientInterface $holidayApiClientService,
        SerializerInterface $serializer,
        ApiRequest $converterHelper
    ) {
        $this->holidayApiClientService = $holidayApiClientService;
        $this->serializer = $serializer;
        $this->converterHelper = $converterHelper;
    }

    /**
     * @Route("/",
     *     methods={"POST"},
     *     requirements={
     *          "country"="\s",
     *          "year" = "\d+"
     *      }
     * )
     * @OA\RequestBody(description="", @Model(type=HolidayRequestForYearType::class)),
     * @OA\Response(
     *     response=200,
     *     description="Returns all holidays for given country and year",
     *     @Model(type=HolidayResponseForYearModel::class)
     * )
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
                        $holidayRequestModel->getYear(),
                        $holidayRequestModel->getCountry()
                    ),
                    200
                )
            );
        }
        return $this->handleView($this->view([$form->getErrors()]));
    }

    /**
     * @Route("/checkDate",
     *      methods={"post"},
     *     requirements={
     *          "country"="\s",
     *          "date" = "\s"
     *      }
     * )
     * @OA\RequestBody(description="", @Model(type=HolidayRequestCheckDateType::class)),
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns type of day of given date",
     *     @OA\JsonContent(
     *          type="string"
     *      )
     * )
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
                        $holidayCheckDateModel->getDateByFormat('d-m-Y'),
                        $holidayCheckDateModel->getCountry()
                    )
                    ,
                    200
                )
            );
        }
        return $this->handleView($this->view([$form->getErrors()], 400));
    }

    /**
     * @Route("/getCount", methods={"post"},
     *          requirements={
     *          "country"="\s",
     *          "year" = "\s"
     *      }
     * )
     * @OA\RequestBody(description="", @Model(type=HolidayRequestForYearType::class)),
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns type of day of given date",
     *        @OA\JsonContent(
     *          type="integer"
     *      )
     * )
     */
    public function getCount(Request $request): Response
    {
        $holidayRequestModel = new HolidayRequestForYearModel();
        $form = $this->createForm(HolidayRequestForYearType::class, $holidayRequestModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleView(
                $this->view(
                    $this->holidayApiClientService->getCountOfFreeDaysAndHolidays(
                        $holidayRequestModel->getYear(),
                        $holidayRequestModel->getCountry()
                    )
                    ,
                    200
                )
            );
        }
        return $this->handleView($this->view([$form->getErrors()], 400));
    }
}
