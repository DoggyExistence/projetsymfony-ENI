<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\TriUtils;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TriUtilsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
				'class'=>Site::class,
				'choice_label'=>'nom',
				'placeholder'=>'Tous',
				'required'=>false
			])
            ->add('motRecherche', TextType::class, [
				'label'=>'Le nom de la sortie contient : ',
				'required'=>false
			])
            ->add('dateDebut', DateType::class, [
				'label'=>'Entre le',
				'required'=>false
			])
            ->add('dateFin', DateType::class, [
				'label'=>'Et le',
				'required'=>false
			])
            ->add('isOrga', CheckboxType::class, [
				'label'=>'Sorties dont je suis l\'organisateur·ice',
				'required'=>false
			])
            ->add('isInscrit', CheckboxType::class, [
				'label'=>'Sorties auxquelles je suis inscrit·e',
				'required'=>false
			])
            ->add('isNonInscrit', CheckboxType::class, [
				'label'=>'Sorties auxquelles je ne suis pas inscrit·e',
				'required'=>false
			])
            ->add('isPassee', CheckboxType::class, [
				'label'=>'Sorties passées',
				'required'=>false
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TriUtils::class,
        ]);
    }
}
