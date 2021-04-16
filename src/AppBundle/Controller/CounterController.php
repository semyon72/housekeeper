<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity;
use AppBundle\Form\Counter\CounterType;
use AppBundle\Form\Filter\CounterFilterType;

use AppBundle\Classes\SupposedDataOfCounter;
use AppBundle\Classes\CheckerOfRestrictionsForCounter;
use AppBundle\Classes\ControllerTrait\SettingsControllerTrait;

/**
 * @Route("counter")
 */
class CounterController extends Controller {
    use SettingsControllerTrait;
    
    /**
     * @Route("/list", name="counter_list")
     */
    public function indexAction(Request $request){

        $em= $this->getDoctrine()->getManager();
        
        $filter_form= $this->createForm(CounterFilterType::class);
        $filter_form= $this->setDefaultPlace($filter_form)->setFilterAndHandleRequest($filter_form, $request);

        $criteria = array_merge(['dateB'=>null,'dateE'=>null,'place'=>null,'service'=>null,'valueName'=>null],(array)$filter_form->getData());
        $counters = $em->getRepository(Entity\Counter::class)->findFiltered($criteria);
        
        return $this->render('counter/list.html.twig', ['counters'=>$counters, 'filter_form'=> $filter_form->createView()] );
    }
    
    
    /**
     * @Route("/{id}", name="counter_newEdit", requirements={"id"="\d+"})
     */
    public function newEditAction(Request $request, $id = null)
    {
        $em= $this->getDoctrine()->getManager();
        
        $counter = new Entity\Counter();
        if ( !is_null($id) ) {
            $counter= $em->find(Entity\Counter::class,$id);
        } else {
            $counter->setValue(0);
        }
        
        $form = $this->createForm(CounterType::class, $counter);
        $form->handleRequest($request);
        if ( $form->isSubmitted() ) {
            if ( $form->isValid() ){
                $recalculate = $form->get('recalculate')->getData();
                if ( $recalculate ){
                    (new SupposedDataOfCounter($em, $form))->Check();
                }

                //finall check restrictions
                (new CheckerOfRestrictionsForCounter($em, $form))->check();

                if ( $form->isValid() ){

                    $em->persist($counter);
                    $em->flush();

                    return $this->redirectToRoute('counter_list');
                }
            }
        } else {
            if ( is_null($id) ) {
                $this->setDefaultPlace($form);
                $form->get('recalculate')->setData(true);
            }
        }        
        $fview= $form->createView();
        return $this->render('counter/new_edit.html.twig', [
            'form' => $fview
        ]);                
        
    }


    /**
     * @Route("/delete/{id}", name="counter_delete", requirements={"id"="\d+"})
     */
    public function deleteAction(Entity\Counter $counter){
        $em= $this->getDoctrine()->getManager();
        
        $em->remove($counter);
        $em->flush();
        
        return($this->redirectToRoute('counter_list'));
    }

    
}
