<?php

namespace App\Form;

use App\Entity\AdCategory;
use App\Entity\AdSubCategory;
use App\Entity\Advert;
use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('title', TextType::class,
                array(
                    'attr' => array(
                        'placeholder' => 'İlanınızı özetleyen başlık giriniz...'
                    ),
                    'required' => true

                )
            )
            ->add('owner', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'İlanda gösterilecek, ilan sahibine ait isim giriniz...'
                ),
                'required' => true
            ))
            ->add('price', IntegerType::class)
            ->add('place', EntityType::class, array(
                'class' => Place::class,
                'choice_label' => function (Place $place) {
                    return $place->getName();
                },
                'required' => true,
                'placeholder' => 'Seçiniz',
            ))
            ->add('secretEmail', CheckboxType::class, array('required' => false,
                'label' => 'İlanda Göster', 'label_attr' => array('style' => 'margin-left:5px;')))
            ->add('secretPhone', CheckboxType::class, array('required' => false,
                'label' => 'İlanda Göster', 'label_attr' => array('style' => 'margin-left:5px;')))
            ->add('secretPrice', CheckboxType::class, array('required' => false,
                'label' => 'İlanda Göster', 'label_attr' => array('style' => 'margin-left:5px;')))
            ->add('featured_image', FileType::class, array('data_class' => null, 'required' => false))
            ->add('description')
            ->add('telephone', TelType::class, array('required' => true))
            ->add('email', EmailType::class)
            ->add('status', ChoiceType::class, array(
                'choices' => [
                    'Satılık' => 'for_sale',
                    'Kiralık' => 'for_rent',

                ],
                'placeholder' => 'Seçiniz'
            ))
            ->add('category', EntityType::class, array(
                'required' => true,
                'placeholder' => 'Seçiniz',
                'class' => AdCategory::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('ac')
                        ->orderBy('ac.sort', 'asc');
                },
                'choice_value' => 'id',
                'choice_translation_domain' => 'advert'))
            ->add('sub_category', EntityType::class, array(
                    'required' => true,
                    'placeholder' => 'Seçiniz',
                    'class' => AdSubCategory::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->orderBy('s.sort', 'asc');
                    },
                    'choice_value' => 'id',
                    'choice_translation_domain' => 'advert')

            );


    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
