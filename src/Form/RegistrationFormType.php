<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'required'=>true
            ])
            ->add('lastname', TextType::class, [
                'required'=>true
            ])
            ->add('email', EmailType::class, [
                'required'=>true,
                'empty_data'=>''
            ])

            ->add('userName', TextType::class, [
                'required'=>true
            ])

            ->add('plainPassword', RepeatedType::class, [
                /*'help' => 'En az 6 karakter',*/
                'type' => PasswordType::class,
                'invalid_message' => 'Girdiğiniz parolalar uyuşmadı. Lütfen tekrar deneyiniz.',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Lütfen parola belirleyiniz.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Şifreniz en az {{ limit }} karakter olmalıdır.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Kullanım koşularını kabul etmelisiniz.',
                    ]),
                ]
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
