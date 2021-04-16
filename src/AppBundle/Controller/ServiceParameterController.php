<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form;

use AppBundle\Entity;
use AppBundle\Form\Service\ServiceParameterType;
use AppBundle\Classes\ControllerTrait\ServiceParameterControllerTrait;
use AppBundle\Classes\Calculator\CumulativeCalculator;

/**
 * Generated: Feb 14, 2019 11:25:21 PM
 * 
 * Description of ServiceParameterController
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 * 
 * @Route("serviceparameter")
 */
class ServiceParameterController extends Controller {
    use ServiceParameterControllerTrait;
    
    /**
     * @Route("/list/{serviceId}", name="serviceparameter_list", requirements={"serviceId"="\d+"})
     */
    public function indexAction(Request $request, $serviceId){

        $em= $this->getDoctrine()->getManager();
        $service= $em->find(Entity\Service::class,$serviceId);
        
        if ( !$this->isParameterizedService($service) ){
            return $this->redirectToRoute('service_list');
        }
        
        $parameters = ['parameters'=>null];
        if (!is_null($service)){
            if ( $service->getCalculator() === CumulativeCalculator::guid() ) {
                $parameters['services']= $this->getServicesListExcludeCurrent($service);
            }
            $serviceParameter = $em->getRepository(Entity\ServiceParameter::class)->findBy(['service'=>$serviceId]);
            $parameters= array_merge($parameters,['parameters'=>$serviceParameter, 'currentService' => $service]);
        } 
        return $this->render('service_parameter/list.html.twig', $parameters );
    }
    
    
    /**
     * @Route("/{serviceId}", name="serviceparameter_new", requirements={"serviceId"="\d+"})
     */
    public function newAction(Request $request, $serviceId)
    {
        $em= $this->getDoctrine()->getManager();
        $service= $em->find(Entity\Service::class,$serviceId);
        
        if ( !$this->isParameterizedService($service) ){
            return $this->redirectToRoute('service_list');
        }
        
        $serviceParameter = new Entity\ServiceParameter();
        if ( !is_null($service) ) {
            $serviceParameter->setService($service);
        }
        $form = $this->createForm(ServiceParameterType::class, $serviceParameter, ['entity_manager'=>$em]);
        $this->tweakParameter($form, $service)->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ){
            $em->persist($serviceParameter);
            $em->flush();

            return $this->redirectToRoute('serviceparameter_list',['serviceId'=>$serviceParameter->getService()->getId()]);
        }
        
        if ( is_null($service) ) {
            $form->addError( new Form\FormError('Service was not found. :( '));
        }
        return $this->render('service_parameter/new.html.twig',[ 'form' => $form->createView(), 'service' => $service ]);                
        
    }

    /**
     * @Route("/edit/{id}", name="serviceparameter_edit", requirements={"id"="\d+"})
     */
    public function editAction(Request $request, $id)
    {
        $em= $this->getDoctrine()->getManager();
        
        $serviceParameter = $em->find(Entity\ServiceParameter::class, $id);
        if ( is_null($serviceParameter) ) {
            return $this->redirectToRoute('service_list');
        }
        $form = $this->createForm(ServiceParameterType::class, $serviceParameter, ['entity_manager'=>$em]);
        $this->tweakParameter($form, $serviceParameter->getService(), $serviceParameter)->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ){
            $em->persist($serviceParameter);
            $em->flush();

            return $this->redirectToRoute('serviceparameter_list',['serviceId'=>$serviceParameter->getService()->getId()]);
        }
        
        return $this->render('service_parameter/edit.html.twig',[ 
            'form' => $form->createView(),
            'service' => $serviceParameter->getService()
        ]);                
    }
    
    /**
     * @Route("/delete/{id}", name="serviceparameter_delete", requirements={"id"="\d+"})
     */
    public function deleteAction(Entity\ServiceParameter $serviceParameter){
        $em= $this->getDoctrine()->getManager();
        
        $em->remove($serviceParameter);
        $em->flush();
        
        return($this->redirectToRoute('serviceparameter_list',['serviceId'=>$serviceParameter->getService()->getId()]));
    }
    
}
