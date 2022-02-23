<?php

namespace App\Controller\api;

use App\Interfaces\CountryHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
class CountryController extends AbstractController
{
    private $countryHelper;

    public function __construct(CountryHelperInterface $countryHelper)
    {
        $this->countryHelper = $countryHelper;
    }

    /**
     * @Route("/api/countries")
     */
    public function test(){
        return new JsonResponse($this->countryHelper->getCountries());
    }
    
}