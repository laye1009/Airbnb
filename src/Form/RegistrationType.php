<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends AbstractType
{

    private function getConf($label,$placeholder)
    {
        return [
            'label'=>$label,
            'attr'=>[
                'placeholder'=>$placeholder,
                'value' => $placeholder
            ]
            ];
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class,$this->getConf("Prénom","Votre prénom .... "))
            ->add('lastName',TextType::class,$this->getConf("Nom","Votre nom de famille... "))
            ->add('email',EmailType::class,$this->getConf("Email","Votre adresse mail"))
            ->add('picture',UrlType::class,$this->getConf("Photo de porfil","https://picsum.photos/seed/picsum/200/300"))
            ->add('hash', PasswordType::class,$this->getConf("Mot de passe","Choix de mot ded passe"))
            ->add('passwordConfirm',PasswordType::class,$this->getConf("confirmation de mot de passe","confirmez votre mot de passe"))
            ->add('introduction',TextType::class,$this->getConf("Introduction","Votre into please... "))
            ->add('description',TextareaType::class,$this->getConf("Description détaillée","description c'est par là"))
       
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
