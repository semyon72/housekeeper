<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;

use AppBundle\Entity\Tariff;
use AppBundle\Entity\TariffValue;
use AppBundle\Form\Tariff\TariffValueType;
use AppBundle\Classes\Calculator\MultiTariffTotalCalculator;
use AppBundle\Classes\Calculator\Range\RangeItemCollection;


/**
 * Generated: Feb 18, 2019 7:06:45 PM
 * 
 * Description of TariffValueController
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 * 
 * @Route("tariffvalue")
 */
class TariffValueController extends Controller{
    
    /**
     * 
     * @param Request $request
     * @param integer $tariffId
     * @return \Symfony\Component\HttpFoundation\Response Description
     * 
     * @Route("/list/{tariffId}", name="tariffvalue_list", requirements={"tariffId"="\d+"})
     * 
     */
    public function indexAction(Request $request, $tariffId ) {
        $em = $this->getDoctrine()->getManager();
        
        $currentTariff= null;
        $tariff = $em->getRepository(Tariff::class)->findActiveBy(['id'=>$tariffId]);
        if ( count($tariff) >0 ) {
            $currentTariff = $tariff[0]['tariff'];
        }
        
        return $this->render('tariff_value/list.html.twig',['tariffValues'=> is_null($currentTariff) ? null : $currentTariff->getTariffValues(), 'currentTariff'=> $currentTariff]);
    }
    
    /**
     * 
     * 
     * @param Request $request
     * @param integer $tariffId
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @Route("/new/{tariffId}", name="tariffvalue_new", requirements={"tariffId":"\d+"} )
     *
     */
    public function newAction(Request $request, $tariffId){
        $em = $this->getDoctrine()->getManager();
        $tariff = $em->find(Tariff::class, $tariffId);
        if ( is_null($tariff)){
            return $this->redirectToRoute('tariff_list');
        }
        
        $tariffValue = new TariffValue();
        $tariffValue->setTariff($tariff);
        $form = $this->createForm(TariffValueType::class, $tariffValue, ['entity_manager'=>$em]);
        $form->handleRequest($request);
        if( $form->isSubmitted() ){
            
            //check the intersections of ranges
            $totalCalc= (new MultiTariffTotalCalculator())->setItems($tariff->getTariffValues());
            if( !$totalCalc->addItem($tariffValue) ){
                $form->addError( new FormError($totalCalc->getItems()->getLastError()) );
            }
            //END check the intersections of ranges
            
            if( $form->isValid()){
                $em->persist($tariffValue);
                $em->flush($tariffValue);
                return $this->redirectToRoute('tariffvalue_list', ['tariffId'=>$form->get('tariff')->getData()->getId()] );
            }
        }
        
        return $this->render('tariff_value/new.html.twig', ['form'=>$form->createView(), 'currentTariff'=> $tariffValue->getTariff()]);
    }
    
    /**
     * 
     * 
     * @param Request $request
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @Route( "/edit/{id}", name="tariffvalue_edit", requirements={"id": "\d+"} )
     * 
     */
    public function editAction(Request $request, $id){

        $em = $this->getDoctrine()->getManager();
        $tariffValue = $em->find(TariffValue::class, $id);
        if ( is_null($tariffValue)){
            return $this->redirectToRoute('tariff_list');
        }
        
        $form = $this->createForm(TariffValueType::class, $tariffValue, ['entity_manager'=>$em]);
        $form->handleRequest($request);
        if( $form->isSubmitted() ){
            
            //check the intersections of ranges
            $persistedTariffValues = $tariffValue->getTariff()->getTariffValues();
            $totalCalc = new MultiTariffTotalCalculator();
            foreach ($persistedTariffValues as $key=>$persistedTariffValue){
                if ( $persistedTariffValue->getId() !== $tariffValue->getId() ){
                    if ( !$totalCalc->addItem($persistedTariffValue) ) {
                        $form->addError( new FormError('Ranges by tariffs that stored in database are intersect.') );
                    }
                }
            }
            if( !$totalCalc->addItem($tariffValue) ){
                $form->addError( new FormError($totalCalc->getItems()->getLastError()) );
            }
            //END check the intersections of ranges
            
            if( $form->isValid()){
                $em->flush($tariffValue);
                return $this->redirectToRoute('tariffvalue_list', ['tariffId'=>$form->get('tariff')->getData()->getId()] );
            }
        }
        
        return $this->render('tariff_value/edit.html.twig', ['form'=>$form->createView(), 'currentTariff'=> $tariffValue->getTariff()]);
    }
    
    /**
     * 
     * @param Request $request
     * @param integer $id Id (primary key) of value of tariff that have to delete. 
     * @return \Symfony\Component\HttpFoundation\Response 
     * 
     * @Route("/delete/{id}", name="tariffvalue_delete", requirements={"id": "\d+"} )
     * 
     */
    public function deleteAction(Request $request, $id )  {
        
        $em = $this->getDoctrine()->getManager();
        $tariffValue= $em->find(TariffValue::class,$id);
        
        $tariffId = null;
        if ( !is_null($tariffValue) ){
            $tariffId = $tariffValue->getTariff()->getId();
            $em->remove($tariffValue);
            $em->flush($tariffValue);
        } else {
            return $this->redirectToRoute('tariff_list');
        }
        
        return $this->redirectToRoute('tariffvalue_list',['tariffId'=>$tariffId]);
    }
}
