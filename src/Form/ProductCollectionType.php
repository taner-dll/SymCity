<?php

namespace App\Form;

use App\Entity\CollectionGroup;
use App\Entity\ProductCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'Koleksiyon İsmi - Örnek: BUBBLE SERIES WHITE / BUBBLE SERIES (TEKİL)'))
            ->add('model_code', TextType::class, array('label' => 'Model Kodu (Örnek: BB01R)'))

            ->add('collectionGroup', EntityType::class,
                array(
                    'class' => CollectionGroup::class,
                    'choice_label' => function ($collectionGroup) {
                        return $collectionGroup->getName();
                    },
                    'label' => 'Koleksiyon Grubu',
                    'required' => true
                )
            )
            ->add('picture', FileType::class, array('data_class' => null, 'label' => 'Resim Seçimi (640x167)', 'required' => false));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductCollection::class,
        ]);
    }
}
