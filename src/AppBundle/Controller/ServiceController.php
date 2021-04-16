<?php

namespace AppBundle\Controller;

use AppBundle\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\Service\ServiceType;
use AppBundle\Classes\Calculator\Calculators;

/**
 * 
 * @Route("service")
 */
class ServiceController extends Controller
{
    
    /**
     * @Route("/list", name="service_list", methods={"GET"})
     */
    public function indexAction(Request $request){
        
        $em= $this->getDoctrine()->getManager();
        $services = $em->getRepository(Entity\Service::class)->findAllWithParametersCount();
        
        return $this->render('service/list.html.twig', ['services'=>$services, 'calculators'=> array_flip(Calculators::getCollectionCalculators())] );
    }
    
    
    /**
     * @Route("/{id}", name="service_newEdit", requirements={"id"="\d+"}, methods={"GET","POST","PUT"})
     */
    public function newEditAction(Request $request, $id = null)
    {
        $em= $this->getDoctrine()->getManager();
        
        $service = new Entity\Service();
        if ( !is_null($id) ) {
            $service= $em->find(Entity\Service::class,$id);
        } else {
            $service->setName('');
        }
        
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);        
        if ( $form->isSubmitted() && $form->isValid() ){
            
            $em->persist($service);
            $em->flush();

            return $this->redirectToRoute('service_list');
        }
        
        $fview= $form->createView();
        return $this->render('service/new_edit.html.twig', [
            'form' => $fview
        ]);                
        
    }


    /**
     * @Route("/delete/{id}", name="service_delete", requirements={"id"="\d+"}, methods={"GET","POST","DELETE"})
     */
    public function deleteAction(Entity\Service $service){
        $em= $this->getDoctrine()->getManager();
        
        $rService = $em->getRepository(get_class($service));
        if ( !$rService->hasChildren($service->getId()) ) {
           $em->remove($service);
           $em->flush();
        }
        
        return($this->redirectToRoute('service_list'));
    }

    
    
}

