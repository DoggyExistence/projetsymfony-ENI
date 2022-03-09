<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DateTimeParamConverter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeImmutableToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'label'=> "Nom de la sortie :",
                'attr' =>
                    ['placeholder'=> "votre nom de sortie"
                ]
            ] )
            ->add('dateHeureDebut', DateTimeType::class,[
                'label'=> "Date et heure de la sortie :",

            ] )

            ->add('dateLimiteInscription', DateType::class,[
                'label'=> "Date limite d'inscription :",

            ] )
            ->add('nbInsciptionsMax', TextType::class,[
                'label'=> "Nombre d'inscription maximum :",
                'attr' =>
                    ['placeholder'=> "Nombre de places"
                ]
            ] )
            ->add('duree', IntegerType::class,[
                'label'=> "Durée (en minutes)",
                'attr' =>
                    ['placeholder'=> "La durée de la sortie "
                    ]
            ] )
            ->add('infosSortie', TextareaType::class,[
                'label'=> "Description et infos :",
                'attr' =>
                    ['placeholder'=> "Commentaire..."
                ]
            ] )
//            ->add('lieu', TextType::class,[
//                'label'=> "Lieu :",
//                'attr' =>
//                    ['placeholder'=> "Veuillez choisir un lieu"
//                ]
//            ] )
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => 'choisir un lieu'
            ])
//            ->add('participants', TextType::class,[
//                'label'=> "Nombre de participants :",
//                'attr' => ['placeholder'=> "Liste d'inscris"
//                ]
//            ] )
//
//            ->add('organisateur', TextType::class,[
//                'label'=> "Organisateur : de la sortie",
//                'attr' =>
//                    [ 'placeholder' => "Entrer votre nom"
//                ]
//            ] )

            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
