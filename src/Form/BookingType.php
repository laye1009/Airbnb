<?php

namespace App\Form;

use DateTime;
use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Intl\DateFormatter\DateFormat\Transformer;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BookingType extends AbstractType
{
    /*
    private $transformer;
    public function __construct(FrenchToDateTransformer $transformer){
        $this->transformer=$transformer;
    }
    */


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('startDate',DateType::class,["widget"=>"single_text"])
            ->add('endDate',DateType::class,["widget"=>"single_text"])
            ->add('comment',TextareaType::class,["required"=>false]);
        /*
            ->add('startDate',TextType::class)
            ->add('endDate',TextType::class)
            ->add('comment',TextareaType::class,["required"=>false]);

        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
        
        $builder->get('startDate')
                ->addModelTransformer(new CallbackTransformer(
                    function($date) {
                        // transform the array to a string
                        if($date===null){
                            return '';
                        }
                        return $date->format('d/m/Y');
                    },
                    function($value){
                        if($value===null){
                            //Exception
                            throw new TransformationFailedException("fournissez une date");
                        }
                
                        $date=\DateTime::createFromFormat('d/m/Y',$value);
                        if($date===false){
                            //exception
                            throw new TransformationFailedException("le format de la date n'est pas bon");
                        }
                
                        return $date;
                        
                    }));
                    */
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'calidation_groups'=>[
                'Default',
                'front'
            ]
        ]);
    }
}
