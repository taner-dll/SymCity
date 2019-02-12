<?php

namespace App\Form;

use App\Entity\PlacesToVisit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlacesToVisitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('about')
            ->add('maps')
            ->add('phone')
            ->add('web', UrlType::class)
            ->add('email', EmailType::class)
            ->add('address')
            ->add('featured_picture', FileType::class, array('data_class' => null, 'required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlacesToVisit::class,
        ]);
    }
}
