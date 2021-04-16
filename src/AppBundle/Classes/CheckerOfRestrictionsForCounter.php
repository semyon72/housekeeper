<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes;

use Symfony\Component\Form\FormError;

/**
 * Generated: Feb 11, 2019 4:15:15 PM
 * 
 * Description of CheckerOfRestrictionsForCounter
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class CheckerOfRestrictionsForCounter extends CounterCheckerDependentService {
    use DateTimeRangeTrait;
    
    //range
    protected function _9336c72c_ced9_4d15_8578_1947a6b28771(CheckerOfRestrictionsForCounter $self){
        $data = $this->counterForm->getData();
        $lastCounter = $self->getLastCounter();
        if( !is_null($lastCounter) ){
            $error = null;
            if (floatval($lastCounter->getValue()) >= floatval($data->getValue()) ){
                $error = new FormError('Exists record whose value more than proposed');
            } else if ($lastCounter->getOnDate() >= $data->getOnDate() ){
                $error = new FormError('Exists record whose value less but has date more than proposed record.');
            }
            if (!is_null($error) ) {
                $self->counterForm->addError($error);
            }
        }
        return $self;
    }

    //fixed
    protected function _20fc5e3a_51f6_493f_9c59_04a51ea735ef(CheckerOfRestrictionsForCounter $self){
        $data = $this->counterForm->getData();
        
        $dateRange = $this->getTimeRangeFixedOn($data->getOnDate());;
        $lastCounter = $self->getLastCounter(
                'onDate',
                ':dateBegin <= cntr.onDate AND cntr.onDate <= :dateEnd ',
                ['dateBegin'=>$dateRange['begin'], 'dateEnd'=>$dateRange['end']]
            );
        if( !is_null($lastCounter) ){
            $self->counterForm->addError( new FormError('Exists record with same time range.') );
        }
        return $self;
    }
    
    //cumulative
    protected function _db5bf992_22ad_4917_bf13_71da1df21959(CheckerOfRestrictionsForCounter $self){
        return $self;
    }
    
    
}
