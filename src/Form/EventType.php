<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('place', EntityType::class, array(
                'class' => Place::class,
                'choice_label' => function(Place $place) {
                    return $place->getName();
                },
                'required' => false,
                'placeholder' => ''
            ))
            ->add('description')
            ->add('start', DateTimeType::class, array(
                'data' => new \DateTime('now')
            ))
            ->add('end', DateTimeType::class, array(
                'data' => new \DateTime('now')
            ))
            ->add('image', FileType::class, array('data_class' => null, 'required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
