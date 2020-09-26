<?php

namespace App\Form;

use App\Entity\Place;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('about')
            ->add('map')
            ->add('featured_image', FileType::class, array('data_class' => null, 'required' => false))
            ->add('type', ChoiceType::class, array(
                'choices' => [
                    'İlçe' => 'district',
                    'Mahalle' => 'neighborhood'
                ]
            ))
            ->add('parent', EntityType::class, array(
                'class'=>Place::class,
                'query_builder'=>function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where('p.type = :type')
                        ->setParameter('type','district')
                        ->orderBy('p.name','ASC')
                        ;
                },
                'choice_label'=>'name',
                'placeholder'=>'',
                'required'=>false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
