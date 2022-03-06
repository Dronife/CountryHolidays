<?php

namespace App\Controller\api;

use App\Interfaces\CountryHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Response\Country\CountryResponseAllSupported;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class CountryController extends AbstractController
{
    private CountryHelperInterface $countryApiClientService;

    public function __construct(CountryHelperInterface $countryApiClientService)
    {
        $this->countryApiClientService = $countryApiClientService;
    }

    /**
     * @Route("/api/countries" , methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns all holidays for given country and year",
     *     @OA\JsonContent(
     *          type="array",
     *          @OA\Items(type="string")
     *      )
     * )
     */
    public function index(): JsonResponse
    {
        return new JsonResponse($this->countryApiClientService->getCountries(), 200);
    }
    
}