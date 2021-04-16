<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Entity;
use AppBundle\Form\Place\PlaceType;


/**
 * 
 * @Route("place")
 */
class PlaceController extends Controller
{
    /**
     * @Route("/list", name="place_list")
     */
    public function indexAction(Request $request){
        
        $em= $this->getDoctrine()->getManager();
        $places = $em->getRepository(Entity\Place::class)->findAll();
        
        return $this->render('place/list.html.twig', ['places'=>$places] );
    }
    
    
    /**
     * @Route("/{id}", name="place_newEdit", requirements={"id"="\d+"})
     */
    public function newEditAction(Request $request, UserInterface $user, $id = null)
    {
        $em= $this->getDoctrine()->getManager();
        
        $place = new Entity\Place();
        if ( !is_null($id) ) {
            $place= $em->find(Entity\Place::class,$id);
        } else {
            $place->setGeo("")->setName('');
        }
        
        $form = $this->createForm(PlaceType::class, $place);
        $form->handleRequest($request);        
        if ( $form->isSubmitted() && $form->isValid() ){
            
            $em->persist($place);
            $places = $user->getPlaces();
            if ( !$places->contains($place) ) {
                $places->add($place);
            }
            $em->flush();

            return $this->redirectToRoute('place_list');
        }
        
        $fview= $form->createView();
        return $this->render('place/new_edit.html.twig', [
            'form' => $fview
        ]);                
        
    }


    /**
     * @Route("/delete/{id}", name="place_delete", requirements={"id"="\d+"})
     */
    public function deleteAction(Entity\Place $place, UserInterface $user){
        $em= $this->getDoctrine()->getManager();
        
        $rPlace = $em->getRepository(get_class($place));
        if ( !$rPlace->hasChildren($place->getId()) && $user->getPlaces()->contains($place)) {
           if( $user->getPlaces()->contains($place) ){
               //\Doctrine\Common\Collections\ArrayCollSection::
                if ( $user->getPlaces()->removeElement($place) ){
                   $em->remove($place);
                }
           }
           $em->flush();
        }
        
        return($this->redirectToRoute('place_list'));
    }

    
    
}
