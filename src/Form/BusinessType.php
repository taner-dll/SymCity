<?php

namespace App\Form;

use App\Entity\Business;
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
            'Seçiniz'=>'',
            'Restoran / Lokanta' => 'eat',
            'Cafe / Bar' => 'cafe',
            'Otel / Pansiyon / Konaklama' => 'otel',
            'Dükkan / Market' => 'shop',
            'Kamp Yeri' => 'camp',
            'Tur / Seyahat' => 'tour',
            'Sağlık / Güzellik' => 'health',
            'Araç Kiralama' => 'car',
            'Tamir / Servis' => 'service',
            'Taksi' => 'taxi',
            'Hukuk' => 'law',
            'Sigorta' => 'insurance',
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
        ;
    }
                                             
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Business::class,
        ]);
    }
}
