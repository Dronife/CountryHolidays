<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
class CountryController extends AbstractController
{

    /**
     * @Route("/api/test")
     */
    public function test(){
        return new JsonResponse("Testas");
    }
    
}