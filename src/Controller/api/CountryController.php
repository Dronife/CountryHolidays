<?php

namespace App\Controller\api;

use App\Interfaces\CountryApiClientInterface;
use App\Message\Country\AddCountries;
use App\Repository\CountryRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class CountryController extends AbstractFOSRestController
{
    private MessageBusInterface $messageBus;
    private CountryRepository $countryRepository;

    public function __construct(
        MessageBusInterface $messageBus,
        CountryRepository $countryRepository
    ) {
        $this->messageBus = $messageBus;
        $this->countryRepository = $countryRepository;
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
                array_map(function ($country) {
                    return $country->getName();
                }, $this->countryRepository->findAll()),
                Response::HTTP_OK
            )
        );
    }
}
