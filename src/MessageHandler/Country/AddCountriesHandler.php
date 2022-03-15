<?php

namespace App\MessageHandler\Country;

use App\Factory\CountryFactory;
use App\Message\Country\AddCountries;
use App\Model\Response\ApiClient\CountryModel;
use App\Repository\CountryRepository;
use App\Services\ApiRequest;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Constants\KayaposoftApi;

class AddCountriesHandler implements MessageHandlerInterface
{
    private CountryRepository $countryRepository;
    private CountryFactory $countryFactory;
    private ApiRequest $apiRequest;

    public function __construct(
        CountryRepository $countryRepository,
        CountryFactory $countryFactory,
        ApiRequest $apiRequest
    ) {
        $this->countryRepository = $countryRepository;
        $this->countryFactory = $countryFactory;
        $this->apiRequest = $apiRequest;
    }

    public function __invoke(AddCountries $getCountries)
    {
        if ($this->countryRepository->getCount() == 0) {
            $countryModels = $this->apiRequest->get(KayaposoftApi::COUNTRY_URL, 'array<' . CountryModel::class . '>');
            foreach ($countryModels as $countryModel) {
                $country = $this->countryFactory->create($countryModel);
                $this->countryRepository->findOneOrCreate($country);
            }
        }
    }

}
