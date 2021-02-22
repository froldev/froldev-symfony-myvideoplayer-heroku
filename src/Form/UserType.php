<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\DependencyInjection\Loader\Configurator\security;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom : ',
                'attr' => [
                    'placeholder' => 'Veuillez indiquer votre Nom',
                ],
            ]);

        if ($builder->getData()->getEmail() !== User::URL_ADMIN) {
            $builder
                ->add('email', EmailType::class, [
                    'label' => 'Email : ',
                    'attr' => [
                        'placeholder' => 'Veuillez indiquer votre Email',
                    ],
                ]);
        }

        $builder
            ->add('password', PasswordType::class, [
                'label' => 'Mot de Passe : ',
                'attr' => [
                    'placeholder' => 'Veuillez indiquer votre Mot de Passe',
                ],
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirmation du Mot de Passe : ',
                'attr' => [
                    'placeholder' => 'Veuillez confirmer votre Mot de Passe',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
