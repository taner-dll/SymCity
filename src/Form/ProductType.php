<?php

namespace App\Form;

use App\Entity\Pattern;
use App\Entity\Product;
use App\Entity\ProductCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('property', ChoiceType::class,
                array(
                    'label' => 'Ürün Yapısı',
                    'choices' => array('Custom'=>'custom','Collection'=>'collection')
                )
            )
            ->add('name')
            ->add('code')
            ->add('picture', FileType::class, array('data_class' => null, 'required' => false))
            ->add('price')
            ->add('properties')
            ->add('packaging')
            ->add('collection', EntityType::class,
                array(
                    'class' => ProductCollection::class,
                    'empty_data' => null
                )
            )
            ->add('type', ChoiceType::class, array(
                'choices'=>array(

                    'Bowl'=>'bowl',
                    'Mug'=>'mug',
                    'Plate'=>'plate',
                    'Snack'=>'snack',
                    'Tea Pot'=>'teapot',
                    'Tumbler'=>'tumbler',
                    'Tray'=>'tray'
                )
            ))
        ;


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
