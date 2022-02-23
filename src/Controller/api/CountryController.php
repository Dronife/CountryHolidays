<?php

namespace App\Controller\api;

use App\Interfaces\CountryHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class CountryController extends AbstractController
{
    private $countryHelper;

    public function __construct(CountryHelperInterface $countryHelper)
    {
        $this->countryHelper = $countryHelper;
    }

    /**
     * @Route("/api/countries" , methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns all supported names of countries",
     * )
     */
    public function index(){
        return new JsonResponse($this->countryHelper->getCountries());
    }
    
}