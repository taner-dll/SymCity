<?php
namespace App\Form;

use App\Entity\Advert;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('price', IntegerType::class)
            ->add('place', EntityType::class, array(
                'class' => Place::class,
                'choice_label' => function(Place $place) {
                    return $place->getName();
                },
                'required' => false,
                'placeholder' => ''
            ))
            ->add('owner', null, array(
                'attr' => array('placeholder'=>'Ad Soyad')
            ))
            ->add('featured_image', FileType::class, array('data_class' => null, 'required' => false))
            ->add('description')
            ->add('telephone')
            ->add('email', EmailType::class)
            ->add('status', ChoiceType::class, array(
                'choices'  => [
                    'Satılık' => 'for_sale',
                    'Kiralık' => 'for_rent',

                ],
            ))
            ->add('type', ChoiceType::class, array(
                'choices'  => [
                    '2. El Eşya' => 'used_stuff',
                    'Konut' => 'house',
                    'İş Yeri' => 'workplace',
                    'Taşıt' => 'vehicle',
                    'Arsa' => 'plot',
                    'Diğer' => 'others'
                ],
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
