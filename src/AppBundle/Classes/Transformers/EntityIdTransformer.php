<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Transformers;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Generated: Feb 15, 2019 11:38:03 AM
 * 
 * Description of EntityIdTransformer
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class EntityIdTransformer implements DataTransformerInterface {
    
    protected $entityManager = null;
    
    protected $IdFieldName = 'id';
    
    protected $className = '';
    
    
    public function __construct(EntityManagerInterface $entityManager, $dataClass, $id = 'id') {
        $this->entityManager = $entityManager;
        $this->IdFieldName = (string)$id;
        $this->className = (string) $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value) {
        $result = '';
        if ( !is_null($value) ){
            $value = (object) $value;
            $reflector = new \ReflectionClass($value);
            if ( $reflector->hasProperty($this->IdFieldName) && 
                 $reflector->getProperty($this->IdFieldName)->isPublic() ){
                $result = $value->${$this->IdFieldName};
            } else {
                $getterName = 'get'.ucfirst($this->IdFieldName);
                if ($reflector->hasMethod($getterName) && 
                    $reflector->getMethod($getterName)->isPublic()){
                    $result = $reflector->getMethod($getterName)->invoke($value);
                } else {
                    throw new TransformationFailedException('Object hasn\'t public property \''.$this->IdFieldName.'\' or method \''.$getterName.'()\' that will return \'id\' representation.');
                }
            }
        }
        
        return($result);
    }


    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value) {
        $result = null;
        $value = trim((string)$value);
        if( $value !== '' ){
            if(is_numeric($value)){
                $rEntity = $this->entityManager->getRepository($this->className);
                $result = $rEntity->find((int)$value);
                if ( is_null($result) ){
                    throw new TransformationFailedException('Entity \''.$this->className.'\' with \'id\'=\''.$value.'\' not found.');
                }
            } else {
                throw new TransformationFailedException('Id value must be numeric.');
            }
        }
        return($result);
    }

}
