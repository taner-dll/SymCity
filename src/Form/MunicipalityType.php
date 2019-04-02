<?php

namespace App\Form;

use App\Entity\Municipality;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MunicipalityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('about')
            ->add('mayor')
            ->add('mayorPhoto', FileType::class, array('data_class' => null, 'required' => false))
            ->add('featured_picture', FileType::class, array('data_class' => null, 'required' => false))
            ->add('web', UrlType::class)
            ->add('email', EmailType::class)
            ->add('phone')
            ->add('fax')
            ->add('map')
            ->add('extra_info')
            ->add('facebook', UrlType::class)
            ->add('twitter', UrlType::class)
            ->add('instagram', UrlType::class)
            ->add('whatsapp');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Municipality::class,
        ]);
    }
}
