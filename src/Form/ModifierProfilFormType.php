<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifierProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo : ',
                'attr' => [
                    'placeholder' => "Votre pseudo"
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom : ',
                'attr' => [
                    'placeholder' => "Votre prenom"
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom : ',
                'attr' => [
                    'placeholder' => "Votre nom"
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Telephone : ',
                'required' => false,
                'attr' => [
                    'placeholder' => "Votre numero de téléphone"
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Mail : ',
                'attr' => [
                    'placeholder' => "Votre adresse mail"
                ]
            ])
			->add('site', EntityType::class, [
				'class' => Site::class,
				'choice_label' => 'nom',
				'placeholder' => 'choisir un site'
			])
//            ->add('password', PasswordType::class, [
//                'label' => 'Password : ',
//                'attr' => [
//                    'placeholder' => "Votre mot de passe"
//                ]
//            ])
//            ->add('roles', TextType::class, [
//                'label' => 'Roles',
//                'attr' => [
//                    'placeholder' => "Votre role"
//                ]
//            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
