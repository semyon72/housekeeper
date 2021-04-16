<?php

namespace AppBundle\Form\Filter;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use AppBundle\Entity\Service;


class FromToPlaceServiceFilterAbstractType extends PlaceFilterAbstractType
{
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('dateB', DateType::class,[
                        'label'=>'From',
                        'required'=>false,
                        'widget'=>'single_text'  /* , 'date_format'=>'dd.MM.yyyy' */
                    ])
                ->add('dateE', DateType::class,[
                        'label'=>'to',
                        'required'=>false, 
                        'widget'=>'single_text' /* , 'date_format'=>'dd.MM.yyyy' */
                    ])
                ->add('service', EntityType::class, [
                        'required'=>false,
                        // looks for choices from this entity
                        'class' => Service::class,  //'AppBundle:Service',
                        // uses the User.username property as the visible option string
                        'choice_label' => 'name'
                        ]);
        
        parent::buildForm($builder,$options);
        
        $builder->get('dateE')->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $dateE = $event->getData();
            if ( !is_null($dateE) ) {
                $dateE->modify('this day 23:59:59');
            }
        });

    }
    

}
