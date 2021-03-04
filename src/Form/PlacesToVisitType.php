<?php

namespace App\Form;

use App\Entity\Place;
use App\Entity\PlacesToVisit;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlacesToVisitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $data = $builder->getData();

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
                'query_builder' => function (EntityRepository $er) use ($data){
                    return $er->createQueryBuilder('p')
                        ->where('p.type = :nb')
                        ->andWhere('p.parent = :parent')
                        ->setParameter('parent',$data->getPlace())
                        ->setParameter('nb', 'neighborhood')
                        ->orderBy('p.name', 'asc');
                },
                'required' => false,
                'placeholder' => 'Seçiniz',
            ))
            ->add('about')
            ->add('type', ChoiceType::class, array(
                'choices'  => [
                    'Seçiniz' => '',
                    'Doğa & Gezilecek Yer' => 'nature',
                    'Tarihi Mekan & Müzeler' => 'history',
                    'Sahil ve Plajlar' => 'sea',
                    'Kültür ve Sanat' => 'culture',
                    'Tur Rotaları' => 'tour',
                ],
            ))
            ->add('phone')
            ->add('web', UrlType::class, array('required'=>false))
            ->add('email', EmailType::class, array('required'=>false))
            ->add('address')
            ->add('mapsEmbedStr')
            ->add('featured_picture', FileType::class, array('data_class' => null, 'required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlacesToVisit::class,
        ]);
    }
}
