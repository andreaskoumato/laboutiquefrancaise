<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addresses', EntityType::class, [
                'label' => 'Choisissez votre adresse de livraison',
                'required' => true,
                'class' => Address::class,
                'expanded' => true,
                'choices' => $options['addresses'],
                'label_html' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'addresses' => null
        ]);
    }
}
