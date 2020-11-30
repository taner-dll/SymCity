<?php

namespace App\Form;

use App\Entity\AdCategory;
use App\Entity\AdSubCategory;
use App\Entity\Advert;
use App\Entity\Place;
use App\Entity\User;
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
use Symfony\Component\Security\Core\Security;

class AdvertType extends AbstractType
{
    private $em;
    /**
     * @var Security
     */
    private $security;

    /**
     * The Type requires the EntityManager as argument in the constructor. It is autowired
     * in Symfony 3.
     *
     * @param EntityManagerInterface $em
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user_name = (String)$this->security->getUser()->getUsername();
        $user = $this->em->getRepository(User::class)->findOneBy(array('user_name' => $user_name));

        $builder
            ->add('title', TextType::class,
                array(
                    'attr' => array(
                        'placeholder' => 'İlanınızı özetleyen kısa başlık giriniz...'
                    ),
                    'required' => true

                )
            )
            ->add('owner', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'İlanda gösterilecek, ilan sahibine ait isim giriniz...'
                ),
                'required' => true,
                'data' => $user->getFirstname() . ' ' . $user->getLastname()
            ))
            ->add('price', IntegerType::class)


            ->add('secretPrice', CheckboxType::class, array('required' => false,
                'label' => ' ', 'label_attr' => array('style' => 'margin-left:5px;')))
            ->add('secretPhone', CheckboxType::class, array('required' => false,
                'label' => ' ', 'label_attr' => array('style' => 'margin-left:5px;')))
            ->add('secretEmail', CheckboxType::class, array('required' => false,
                'label' => ' ', 'label_attr' => array('style' => 'margin-left:5px;')))




            ->add('featured_image', FileType::class, array('data_class' => null, 'required' => false))
            ->add('description')
            ->add('telephone', TelType::class, array('required' => true))
            ->add('email', EmailType::class, array(
                'required' => true,
                'data' => $user->getEmail()
            ))
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
                    'choice_translation_domain' => 'advert')


            )
            /*->add('sub_category', EntityType::class, array(
                    'required' => true,
                    'placeholder' => 'Seçiniz',
                    'class' => AdSubCategory::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->orderBy('s.sort', 'asc');
                    },
                    'choice_value' => 'id',
                    'choice_translation_domain' => 'advert')

            )*/
            ->add('place', EntityType::class, array(
                'class' => Place::class,
                'choice_label' => function (Place $place) {
                    return $place->getName();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.type = :dst')
                        ->setParameter('dst','district')
                        ->orderBy('p.name', 'asc');
                },
                'required' => true,
                'placeholder' => 'Seçiniz',
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
