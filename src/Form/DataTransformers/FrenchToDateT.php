<?php

namespace App\Form\DataTransformers;
use DateTime;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateT implements DataTransformerInterface{

    public function transform($date){
        if($date===null){
            return '';
        }
        return $date->format('d/m/Y');

    }

    public function reverseTransform($value)
    {
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
        
    }

}