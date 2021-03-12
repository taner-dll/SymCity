<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Place;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$data = $builder->getData();

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
            ->add('description', TextareaType::class, array('required'=>true))
            ->add('start', DateTimeType::class, array(
                'data' => new \DateTime('now')
            ))
            ->add('end', DateTimeType::class, array(
                'data' => new \DateTime('now')
            ))
            ->add('address')
            ->add('mapsEmbedStr')
            ->add('web', UrlType::class, array('required'=>false))
            ->add('phone')

            ->add('image', FileType::class, array('data_class' => null, 'required' => false))
            ->add('category', ChoiceType::class, array(
                'choices' => array(
                    'concert' => 'concert',
                    'sport' => 'sport',
                    'theatre' => 'theatre',
                    'scene' => 'scene',
                    'education' => 'education',
                    'kids' => 'kids',
                    'family' => 'family',
                    'festival' => 'festival',
                    'fair' => 'fair',
                    'trip' => 'trip',
                    'conferance' => 'conferance',
                    'other' => 'other'
                ),
                'required' => true,
                'placeholder' => 'Seçiniz',
                'choice_translation_domain' => 'event'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
