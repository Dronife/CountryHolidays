<?php

namespace App\Controller\api;

use App\Interfaces\HolidayHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class HolidayController extends AbstractController
{
    /**
     * @var HolidayHelperInterface
     */
    private $holidayHelper;

    public function __construct(HolidayHelperInterface $holidayHelper)
    {

        $this->holidayHelper = $holidayHelper;
    }

    /**
     *@Route("/api/holidays", methods={"get"})
     */
    public function holidays(Request $request):JsonResponse{
        return new JsonResponse($this->holidayHelper->getHolidaysByYearAndCountry(2022, 'Angola'));
    }

}