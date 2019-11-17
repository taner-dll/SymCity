<?php

namespace App\Form;

use App\Entity\Business;
use App\Entity\BusinessCategory;
use App\Entity\Place;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name',TextType::class, array('required' => true))
            ->add('map')
            ->add('about')
            ->add('phone',TextType::class, array('required' => true))
            ->add('web', UrlType::class, array('required' => false))
            ->add('email', EmailType::class, array('required' => true))
            ->add('adress',TextType::class, array('required' => true))
            ->add('facebook', UrlType::class, array('required' => false))
            ->add('twitter', UrlType::class, array('required' => false))
            ->add('instagram', UrlType::class, array('required' => false))
            ->add('featured_picture', FileType::class, array('data_class' => null, 'required' => false))
            ->add('category', EntityType::class, array(
                'class'  => BusinessCategory::class,

                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('bc')
                        ->orderBy('bc.sort','asc');
                },
                'choice_label' => 'short_name',
                'placeholder' => 'Seçiniz',
                'required' => true,
                'choice_translation_domain' => 'business'
        
            ))
            ->add('place', EntityType::class, array(
                'class' => Place::class,
                'choice_label' => function(Place $place) {
                    return $place->getName();
                },
                'placeholder' => 'Seçiniz',
                'required' => true

            ))
        ;
    }
                                             
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Business::class,
        ]);
    }
}
