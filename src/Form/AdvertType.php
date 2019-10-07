<?php

namespace App\Form;

use App\Entity\AdCategory;
use App\Entity\AdSubCategory;
use App\Entity\Advert;
use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    private $em;

    /**
     * The Type requires the EntityManager as argument in the constructor. It is autowired
     * in Symfony 3.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('price', IntegerType::class)
            ->add('place', EntityType::class, array(
                'class' => Place::class,
                'choice_label' => function (Place $place) {
                    return $place->getName();
                },
                'required' => true,
                'placeholder' => 'Seçiniz',
            ))
            ->add('owner', null, array(
                'attr' => array('placeholder' => 'Ad Soyad')
            ))
            ->add('featured_image', FileType::class, array('data_class' => null, 'required' => false))
            ->add('description')
            ->add('telephone', TelType::class, array(
                'attr' => array(
                    'pattern' => '[0-9]{3}-[0-9]{3}-[0-9]{4}',
                    //'placeholder' => '000-000-0000'
                ),
                'required' => true


            ))
            ->add('email', EmailType::class)
            ->add('status', ChoiceType::class, array(
                'choices' => [
                    'Satılık' => 'for_sale',
                    'Kiralık' => 'for_rent',

                ],
            ))
            ->add('category', EntityType::class, array(
                    'required' => true,
                    'placeholder' => 'Seçiniz',
                    'class' => AdCategory::class,
                    'choice_value' => 'short_name',
                    'choice_translation_domain' => 'advert'))

            ->add('sub_category', ChoiceType::class, array(
                    'required' => false,
                    'placeholder' => 'Seçiniz',
                    'choice_value' => null)

            );


    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
