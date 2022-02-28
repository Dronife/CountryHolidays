<?php

namespace App\Controller\api;

use App\Form\Type\HolidayRequestType;
use App\Interfaces\HolidayHelperInterface;
use App\Model\HolidayModel;
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
        $holidayRequestModel = new HolidayRequestForYearModel();
        $form = $this->createForm(HolidayRequestType::class, $holidayRequestModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleView(
                $this->view(
                    $this->holidayHelper->getHolidaysByYearAndCountry(
                        $holidayRequestModel->getYear(), $holidayRequestModel->getCountry()),
                    200)
            );
        }
        return $this->handleView($this->view([$form->getErrors()]));
    }

    /**
     * @Route("/api/holidays/checkDate", methods={"post"})
     */
    public function checkDate(Request $request):Response
    {
        return $this->handleView($this->view($request->request->all()));

    }

}