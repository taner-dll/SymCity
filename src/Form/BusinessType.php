<?php

namespace App\Form;

use App\Entity\Business;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $choices = [
            'Yemek' => 'eat',
            'Kafe / Bar' => 'cafe-bar',
            'Otel' => 'hotel',
            'Pansiyon / Konaklama' => 'hostel',
            'Kamp Yeri' => 'camp',
            'Tur / Seyahat' => 'tour-travel',
            'Sağlık / Güzellik' => 'health-beauty',
            'Araç Kiralama' => 'car',
            'Tamir / Bakım / Servis' => 'repair-service',
            'Taksi' => 'taxi',
            'Hukuk' => 'law',
            'Sigorta' => 'insurance',
            'Emlak' => 'real-estate',
            'Market / Büfe' => 'market',
            'Dükkan' => 'shop',
            'Diğer' => 'other'
        ];



        $builder
            ->add('name')
            ->add('map')
            ->add('about')
            ->add('phone')
            ->add('web', UrlType::class, array('required' => false))
            ->add('email', EmailType::class, array('required' => false))
            ->add('adress')
            ->add('facebook', UrlType::class, array('required' => false))
            ->add('twitter', UrlType::class, array('required' => false))
            ->add('instagram', UrlType::class, array('required' => false))
            ->add('featured_picture', FileType::class, array('data_class' => null, 'required' => false))
            ->add('type', ChoiceType::class, array(
                'choices'  => $choices,
                'required' => true,
        
            ))
            ->add('place', EntityType::class, array(
                'class' => Place::class,
                'choice_label' => function(Place $place) {
                    return $place->getName();
                },
                'placeholder' => 'Seçiniz',
                'required' => false

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
