<?php

namespace App\Form;

use App\Entity\Announce;
use App\Entity\Place;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnounceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('place', EntityType::class, array(
                'class' => Place::class,
                'choice_label' => function (Place $place) {
                    return $place->getName();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.type = :dst')
                        ->setParameter('dst', 'district')
                        ->orderBy('p.name', 'asc');
                },
                'required' => true,
                'placeholder' => 'Seçiniz',
            ))
            ->add('subPlace', EntityType::class, array(
                'class' => Place::class,
                'choice_label' => function (Place $place) {
                    return $place->getName();
                },
                'query_builder' => function (EntityRepository $er)  {
                    return $er->createQueryBuilder('p')
                        ->where('p.type = :nb')
                        ->setParameter('nb', 'neighborhood')
                        ->orderBy('p.name', 'asc');
                },
                'required' => false,
                'placeholder' => 'Seçiniz',
            ))
            ->add('description')
            /*->add('image', FileType::class, array('data_class' => null, 'required' => false))*/
            ->add('category', ChoiceType::class, array(
                'choices'=>array(
                    'urgent'=>'urgent',
                    'lost'=>'lost',
                    'discount'=>'discount',
                    'death'=>'death',
                    'other'=>'other'
                ),
                'required' => true,
                'placeholder' => 'Seçiniz',
                'choice_translation_domain'=>'announce'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Announce::class,
        ]);
    }
}
