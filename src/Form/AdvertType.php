<?php
namespace App\Form;

use App\Entity\Advert;
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
            ->add('owner')
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
