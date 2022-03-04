<?php

namespace App\Controller\api;

use App\Form\Type\HolidayRequestCheckDateType;
use App\Form\Type\HolidayRequestForYearType;
use App\Interfaces\HolidayApiClientInterface;
use App\Model\HolidayRequestCheckDate;
use App\Model\HolidayRequestForYearModel;
use App\Services\ModelConverterHelper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Holiday;
use OpenApi\Annotations as OA;


class HolidayController extends AbstractFOSRestController
{

    private HolidayApiClientInterface $holidayApiClientService;
    private SerializerInterface $serializer;
    /**
     * @var ModelConverterHelper
     */
    private ModelConverterHelper $converterHelper;

    public function __construct(HolidayApiClientInterface $holidayApiClientService, SerializerInterface $serializer, ModelConverterHelper $converterHelper)
    {

        $this->holidayApiClientService = $holidayApiClientService;
        $this->serializer = $serializer;
        $this->converterHelper = $converterHelper;
    }

    /**
     * @Route("/api/holidays",
     *     methods={"POST"},
     *     requirements={
     *          "country"="\s",
     *          "year" = "\d+"
     *      }
     * )
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property (property="country", description="Name of available countries", example="England", type="string"),
     *                  @OA\Property (property="year", description="Query all holidays in given year", example="2020", type="integer")
     *             )
     *      )
     * )
     *
     *
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all holidays for given country and year",
     * )
     * @OA\Response(
     *     response=500,
     *     description="Unexpected server error",
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
        }
        return $this->handleView($this->view([$form->getErrors()]));
    }

    /**
     * @Route("/api/holidays/getCount", methods={"post"})
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
                        $holidayRequestModel->getYear(), $holidayRequestModel->getCountry()),
                    200)
            );
        }
        return $this->handleView($this->view([$form->getErrors()]));
    }
}