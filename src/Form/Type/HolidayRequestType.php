<?php

namespace App\Form\Type;


use App\Model\HolidayRequestForYearModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class HolidayRequestType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', TextType::class,
                [
                    'label' => false,
                    'required' => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )->add('year', IntegerType::class,
                [
                    'label' => false,
                    'required' => true,
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThan(2000),
                        new LessThan(3000),
                    ]
                ]);
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => HolidayRequestForYearModel::class,
                'csrf_protection' => false,
                'allow_extra_fields' => true,

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