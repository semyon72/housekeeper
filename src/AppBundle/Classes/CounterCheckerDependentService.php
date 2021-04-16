<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Generated: Feb 12, 2019 12:43:36 AM
 * 
 * Description of CounterCheckerDependentService
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class CounterCheckerDependentService extends CheckerDependentService {
    
    protected $entityManager = null;
    
    protected $counterForm = null;
    
    public function __construct(EntityManagerInterface $entityManager, FormInterface $counterForm) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->counterForm = $counterForm;
    }

    public function getGuid() {
        $data = $this->counterForm->getData();
        return $data->getService()->getCalculator();
    }

    
    protected function getLastCounter($orderBy = 'value', $andWhere = '', array $params = [] ){
        $result= null;
        $data = $this->counterForm->getData();
        $rCounter = $this->entityManager->getRepository(get_class($data));
        $qb = $rCounter->createQueryBuilder('cntr')->addOrderBy('cntr.'.$orderBy,'DESC');
        $qb->where(' cntr.place = :place AND cntr.service = :service');
        $parameters = ['place'=>$data->getPlace(), 'service'=>$data->getService()];
        $isPersisted = $this->entityManager->contains($data);
        if ( $isPersisted ){
            $qb->andWhere(' cntr.id != :id ');
            $parameters['id']= $data->getId();
        }

        if ( $andWhere !== '' ) {
            $qb->andWhere($andWhere);
        }
        if (count($params) > 0) {
            $parameters = array_merge($parameters, $params);
        }
        
        
        $counters = $qb->getQuery()->setParameters($parameters)->setMaxResults(1)->getResult();
        if ( count($counters) > 0 ) {
            $result = $counters[0];
        }
        return $result;
    }    
    
}
