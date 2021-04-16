<?php

namespace AppBundle\Form\PlaceService;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\PaymentInfo; 

class PlaceServiceType extends AbstractType
{
    
    protected $currentPlace = null;
    
    
    private function _getChoicesCallBack(EntityRepository $eRepo){
        return $eRepo->createQueryBuilder('pi')->select('pi','p')->leftJoin('pi.place','p')->orderBy('pi.place', 'ASC')->addOrderBy('pi.priority', 'DESC');
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->currentPlace = $options['current_place'];
        
        $builder->add('place',
                      EntityType::class,
                      ['label'=>'Place', 'class' => 'AppBundle:Place', 'choice_label'=>'name'])
                ->add('service',
                      EntityType::class,
                      ['label'=>'Service', 'class' => 'AppBundle:Service','choice_label'=>'name' ])
                ->add('paymentInfo',
                      EntityType::class,
                      ['label'=>'Payment information',
                        'required'=>false,
                        'class' => PaymentInfo::class,
                        'preferred_choices' => function(PaymentInfo $entity, $key, $value){
                            $result = null;
                            if ( $this->currentPlace !== null && $this->currentPlace->getId() === $entity->getPlace()->getId()  ){
                                $result = $entity;
                            }
                            return $result;
                        }, 
                        'group_by' => function(PaymentInfo $entity, $key, $value){
                            return $entity->getPlace()->getName();
                        }, 
                        'query_builder' => function (EntityRepository $eRepo) {
                            return $this->_getChoicesCallBack($eRepo);
                        },          
//                        'choices'=> $options['payment_info_choices'],
                        'choice_label'=> function($paymentInfo) {
                              return  $paymentInfo->getCountry().', '.$paymentInfo->getCity().', '.
                                      $paymentInfo->getStreet().', '.$paymentInfo->getHouse().', '.
                                      $paymentInfo->getApartment().' -> IBAN: '.$paymentInfo->getIban().', '.
                                      ' priority: '.$paymentInfo->getPriority(); 
                        }
                      ] )
                ->add('save', SubmitType::class, ['label'=>'Save data']);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PlaceService',
            'current_place' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_placeservice';
    }


}
