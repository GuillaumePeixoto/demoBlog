<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if($options['userRegistration'] == true)
        {
            $builder
                ->add('adresse', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => "Veuillez renseigner votre adresse."
                        ])
                    ]
                ])
                ->add('ville', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => "Veuillez renseigner votre ville."
                        ])
                    ]
                ])
                ->add('codePostal', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => "Veuillez renseigner votre code postal."
                        ])
                    ]
                ])
                ->add('prenom', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => "Veuillez renseigner votre prenom."
                        ])
                    ]
                ])
                ->add('nom', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => "Veuillez renseigner votre nom."
                        ])
                    ]
                ])
                ->add('email', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => "Veuillez renseigner votre adresse mail."
                        ])
                    ]
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => "Les mots de passes ne correspondent pas",
                    'required' => false,
                    'options' => [
                        'attr' => [
                            'class' => 'password-field'
                        ]
                    ],
                    'first_options' => [
                        'label' => "Mot de passe"
                    ],
                    'second_options' => [
                        'label' => "Confirmer votre mot de passe"
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => "Veuillez renseigner votre mot de passe."
                        ]),
                        new Length([
                            'min' => 8,
                            'max' => 24,
                            'minMessage' => "Votre mot de passe doit contenir au minimum 8 caract??res.",
                            'maxMessage' => "Votre mot de passe doit contenir au maximum 24 caract??res."
                        ])
                    ]
                ])
            ;
        }
        elseif($options['userUpdate'] == true)
        {
            $builder
            ->add('adresse', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez renseigner votre adresse."
                    ])
                ]
            ])
            ->add('ville', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez renseigner votre ville."
                    ])
                ]
            ])
            ->add('codePostal', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez renseigner votre code postal."
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez renseigner votre prenom."
                    ])
                ]
            ])
            ->add('nom', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez renseigner votre nom."
                    ])
                ]
            ])
            ->add('email', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez renseigner votre adresse mail."
                    ])
                ]
            ]);
        }
        elseif($options['adminUserUpdate'] == true)
        {
            $builder
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => "D??finir le role de l'utilisateur"
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'userRegistration' => false,
            'userUpdate' => false,
            'adminUserUpdate' => false
        ]);
    }
}
