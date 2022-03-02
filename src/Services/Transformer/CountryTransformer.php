<?php

namespace App\Services\Transformer;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CountryTransformer implements DataTransformerInterface
{

    private CountryRepository $countryRepository;

    public function __construct(CountryRepository $countryRepository){

        $this->countryRepository = $countryRepository;
    }

    public function transform($value)
    {
        // TODO: Implement transform() method.
    }

    public function reverseTransform($value) : ?Country
    {
        return $this->countryRepository->findOneBy(['name'=>$value]);
    }
}