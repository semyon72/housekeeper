<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity;
use AppBundle\Form\Tariff\TariffType;
use AppBundle\Classes\ControllerTrait\SettingsControllerTrait;

/**
 * @Route("tariff")
 */
class TariffController extends Controller{
    use SettingsControllerTrait;
    /**
     * @Route("/list/{rowset}", name="tariff_list")
     */
    public function indexAction(Request $request, $rowset = 'active'){
        
        $em= $this->getDoctrine()->getManager();
        $rTariffs = $em->getRepository(Entity\Tariff::class);
        
        if ($rowset ===  'active') {
            $conditions= array('currentDate'=>null);
        }
        $tariffs= $rTariffs->findActiveBy($conditions);
        
        
        return $this->render('tariff/list.html.twig', ['tariffs'=>$tariffs] );
    }
    
    
    /**
     * @Route("/{id}", name="tariff_newEdit", requirements={"id"="\d+"})
     */
    public function newEditAction(Request $request, $id = null)
    {
        $em= $this->getDoctrine()->getManager();
        
        $tariff = new Entity\Tariff();
        if ( !is_null($id) ) {
            $tariff= $em->find(Entity\Tariff::class,$id);
        } else {
            $tariff->setUnitName('')->setUnitValue(0);
        }
        
        $form = $this->createForm(TariffType::class, $tariff);
        $form->handleRequest($request);        
        if ( $form->isSubmitted() ) {
            if ($form->isValid() ){
                $em->persist($tariff);
                $em->flush();

                return $this->redirectToRoute('tariff_list');
            }
        } else {
            if ( is_null($id) ) {
                $this->setDefaultPlace($form);
            }
        }  
        
        $fview= $form->createView();
        return $this->render('tariff/new_edit.html.twig', [
            'form' => $fview
        ]);                
        
    }


    /**
     * @Route("/delete/{id}", name="tariff_delete", requirements={"id"="\d+"})
     */
    public function deleteAction(Entity\Tariff $tariff){
        $em= $this->getDoctrine()->getManager();
        
        $rTariff = $em->getRepository(get_class($tariff));
        if ( !$rTariff->hasChildren($tariff->getId()) ) {
           $em->remove($tariff);
           $em->flush();
        }
        
        return($this->redirectToRoute('tariff_list'));
    }

    
}
