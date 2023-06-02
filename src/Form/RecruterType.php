<?php

namespace App\Form;

use App\Entity\Recruter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class RecruterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('domain', TextType::class)
            ->add('companyName', TextType::class)
            ->add('address', TextType::class)
            ->add('address2', TextType::class, [
                'required' => false,
            ])
            ->add('city', TextType::class)
            ->add('postalCode', NumberType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$/',
                        'message' => 'The postal code is invalide',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recruter::class,
        ]);
    }
}
