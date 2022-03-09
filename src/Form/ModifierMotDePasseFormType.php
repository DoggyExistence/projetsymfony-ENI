<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ModifierMotDePasseFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add('plainPassword', RepeatedType::class, [
				'type' => PasswordType::class,
				'first_options' => [
					'attr' => ['autocomplete' => 'new-password'],
					'constraints' => [
						new NotBlank([
							'message' => 'Veuillez saisir un mot de passe',
						]),
						new Length([
							'min' => 6,
							'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères',
							// max length allowed by Symfony for security reasons
							'max' => 20,
							'maxMessage' => 'Le mot de passe ne doit pas faire plus de {{ limit }} caractères'
						]),
					],
					'label' => 'Nouveau mot de passe',
				],
				'second_options' => [
					'attr' => ['autocomplete' => 'new-password'],
					'label' => 'Confirmez le nouveau mot de passe',
				],
				'invalid_message' => 'Les mots de passe sont différents',
				// Instead of being set onto the object directly,
				// this is read and encoded in the controller
				'mapped' => false,
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
