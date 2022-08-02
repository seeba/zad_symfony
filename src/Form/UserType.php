<?php

namespace App\Form;

use App\Entity\ProgrammingLanguage;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Hasło'
            ])
            ->add('pesel', TextType::class)
            ->add('firstName', TextType::class, [
                'label' => 'Imię'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nazwisko'
            ])
            ->add('programmingLanguages', CollectionType::class, [
                'label' => 'Języki programowania',
                'entry_type' => ProgrammingLanguageType::class,
                'by_reference' => false,
                'entry_options' => ['label' => false],
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
