<?php

namespace App\Form;

use App\Entity\AdCategory;
use App\Entity\AdSubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdSubCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('short_name')
            ->add('active')
            ->add('adCategory', EntityType::class, array(
                'class' => AdCategory::class,
                'choice_label' => function(AdCategory $adCategory){
                    return $adCategory->getShortName();
                }          ,
                'choice_translation_domain' => 'advert'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AdSubCategory::class,
        ]);
    }
}
