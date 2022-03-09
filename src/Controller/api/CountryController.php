<?php

namespace App\Controller\api;

use App\Interfaces\CountryApiClientInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class CountryController extends AbstractFOSRestController
{
    private CountryApiClientInterface $countryApiClientService;

    public function __construct(CountryApiClientInterface $countryApiClientService)
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
    public function index(): Response
    {
        return $this->handleView(
            $this->view(
                $this->countryApiClientService->getCountries(),
                200
            )
        );
    }
}
