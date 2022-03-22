<?php

namespace App\Controller\api;

use App\Form\Type\HolidayRequestCheckDateType;
use App\Form\Type\HolidayRequestForYearType;
use App\Message\Holiday\AddKayaposoftApiHolidaysToCountry;
use App\Model\Request\Holiday\HolidayRequestCheckDateModel;
use App\Model\Request\Holiday\HolidayRequestForYearModel;
use App\Model\Response\Holiday\HolidayResponseForYearModel;
use App\Repository\HolidayRepository;
//use App\Services\HolidayApiClientService;
use App\Services\HolidayManager;
use App\Services\LogicHandlers\Holiday\HolidayControllerLogicHandler;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
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
    private SerializerInterface $serializer;
    private HolidayRepository $holidayRepository;
    private MessageBusInterface $messageBus;
    private HolidayManager $holidayManager;
    private HolidayControllerLogicHandler $controllerLogicHandler;

    public function __construct(
        SerializerInterface $serializer,
        HolidayRepository $holidayRepository,
        MessageBusInterface $messageBus,
        HolidayManager $holidayManager,
        HolidayControllerLogicHandler $controllerLogicHandler
    ) {
        $this->serializer = $serializer;
        $this->holidayRepository = $holidayRepository;
        $this->messageBus = $messageBus;
        $this->holidayManager = $holidayManager;
        $this->controllerLogicHandler = $controllerLogicHandler;
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
            $this->messageBus->dispatch(
                new AddKayaposoftApiHolidaysToCountry(
                    $holidayRequestModel->getYear(),
                    $holidayRequestModel->getCountry()
                )
            );

            return $this->handleView(
                $this->view(
                    $this->holidayRepository->getHolidaysByYearAndCountryName(
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
    public function getDateType(Request $request): Response
    {
        $holidayCheckDateModel = new HolidayRequestCheckDateModel();
        $form = $this->createForm(HolidayRequestCheckDateType::class, $holidayCheckDateModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            return $this->handleView(
                $this->view(
                    $this->controllerLogicHandler->getDateTypeAndSaveHoliday($holidayCheckDateModel),
                    Response::HTTP_OK
                )
            );
        }

        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
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
    public function getMaxFreeDaysCountInRow(Request $request): Response
    {
        $holidayRequestModel = new HolidayRequestForYearModel();
        $form = $this->createForm(HolidayRequestForYearType::class, $holidayRequestModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageBus->dispatch(
                new AddKayaposoftApiHolidaysToCountry(
                    $holidayRequestModel->getYear(),
                    $holidayRequestModel->getCountry()
                )
            );

            $holidays = $this->holidayRepository->getHolidaysByYearAndCountryName(
                $holidayRequestModel->getYear(),
                $holidayRequestModel->getCountry()->getName()
            );

            return $this->handleView(
                $this->view(
                    $this->holidayManager->getCountedFreeDays($holidays, $holidayRequestModel)
                    ,
                    200
                )
            );
        }
        return $this->handleView($this->view([$form->getErrors()], 400));
    }
}
