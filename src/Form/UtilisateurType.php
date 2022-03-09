<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo : ',
                'attr' => [
                    'placeholder' => 'pseudo de l\'utilisateur'
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom : ',
                'attr' => [
                    'placeholder' => 'Nom de l\'utilisateur'
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom : ',
                'attr' => [
                    'placeholder' => 'Prénom de l\'utilisateur'
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Mail : ',
                'attr' => [
                    'placeholder' => 'Mail de l\'utilisateur'
                ]
            ])
            ->add('telephone', TextType::class,[
                'label' => 'Téléphone : ',
                'attr' => [
                    'placeholder' => 'Numéro de téléphone de l\'utilisateur'
                ],
                'required' => false
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password : ',
                'attr' => [
                    'placeholder' => "Mot de passe"
                ]
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'placeholder' => 'choisir un site',
                'label' => 'Site :'
            ])
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                ],
 //               'expanded' => true,
                'multiple' => true,
                'label' => 'Role :',
                'required'=>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
