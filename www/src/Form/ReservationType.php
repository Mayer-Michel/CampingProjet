<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('dateStart', DateType::class, [
            'widget' => 'single_text', // Ensures correct format
            'required' => true, // Make it required
        ])
        ->add('dateEnd', DateType::class, [
            'widget' => 'single_text',
            'required' => true,
        ])
        ->add('type', ChoiceType::class, [
            'choices' => [
                'Mobile-home' => 10,
                'Tente meublée' => 11,
                'Emplacement nus' => 12,
            ],
            'required' => false,
        ])
        ->add('adults', IntegerType::class, [
            'required' => true,
            'empty_data' => '1',
        ])
        ->add('kids', IntegerType::class, [
            'required' => true,
            'empty_data' => '0',
        ]);
    }
}