<?php

namespace App\Form;

use App\Entity\Recruter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecruterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('domain')
            ->add('companyName')
            ->add('address')
            ->add('address2')
            ->add('city')
            ->add('postalCode')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recruter::class,
        ]);
    }
}
