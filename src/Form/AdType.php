<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends AbstractType
{

    private function getConf($label,$placeholder)
    {
        return [
            'label'=>$label,
            'attr'=>[
                'placeholder'=>$placeholder
            ]
            ];
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,[
                'label'=>'Titre',
                'attr'=>['placeholder'=>"Tapez un super titre pour votre annonce!"]
            ])
            ->add('slug',TextType::class,$this->getConf("adresse Web","Tapez l'adresse web"))
            ->add('price')
            ->add('introduction')
            ->add('content')
            ->add('coverImage')
            ->add('rooms')
            ->add(
                'images',
                CollectionType::class,[
                    'entry_type'=>ImageType::class,
                    'allow_add'=>true,
                    'allow_delete'=>true
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}