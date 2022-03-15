<?php

namespace App\Controller\api;

use App\Interfaces\CountryApiClientInterface;
use App\Message\Country\AddCountries;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class CountryController extends AbstractFOSRestController
{
    private CountryApiClientInterface $countryApiClientService;
    private MessageBusInterface $messageBus;

    public function __construct(CountryApiClientInterface $countryApiClientService, MessageBusInterface $messageBus)
    {
        $this->countryApiClientService = $countryApiClientService;
        $this->messageBus = $messageBus;
    }

    /**
     * @Route("/countries" , methods={"GET"})
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
        $this->messageBus->dispatch(new AddCountries());
        return $this->handleView(
            $this->view(
                $this->countryApiClientService->getCountries(),
                200
            )
        );
    }
}
