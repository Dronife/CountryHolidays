<?php

namespace App\Form\Type;

use App\Model\HolidayRequestCheckDate;
use App\Services\Transformer\CountryTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class HolidayRequestCheckDateType extends AbstractType
{

    private CountryTransformer $countryTransformer;

    public function __construct(CountryTransformer $countryTransformer){

        $this->countryTransformer = $countryTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', DateType::class, [
            'widget' => 'single_text',
            'input_format' => 'string',
            'format' => "yyyy-MM-dd",
            'required'=> true,
            'html5' => false,
            'label' => false,
            'constraints' => [
                new NotBlank()
            ]
        ])
            ->add('country', TextType::class,
                [
                    'label' => false,
                    'required' => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );
        $builder->get('country')
            ->addModelTransformer($this->countryTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => HolidayRequestCheckDate::class,
                'csrf_protection' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return '';
    }
}