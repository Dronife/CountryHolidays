<?php

namespace App\Form\Type;


use App\Form\Transformer\CountryTransformer;
use App\Model\Request\Holiday\HolidayRequestYearModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class HolidayRequestForYearType extends AbstractType
{
    private CountryTransformer $countryTransformer;

    public function __construct(CountryTransformer $countryTransformer)
    {
        $this->countryTransformer = $countryTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )->add(
                'year',
                IntegerType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThan(2010),
                        new LessThan(3000),
                    ]
                ]
            );

        $builder->get('country')
            ->addModelTransformer($this->countryTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => HolidayRequestYearModel::class,
                'csrf_protection' => false,
                'allow_extra_fields' => true,

            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
