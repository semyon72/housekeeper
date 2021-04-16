<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity;
use AppBundle\Form\PlaceService\PlaceServiceType;
use AppBundle\Form\Filter\PlaceServiceFilterType;
use AppBundle\Form\PlaceService\PlaceServiceMarkForReceiptCollectionType;
use Symfony\Component\Form\FormError;
use AppBundle\Classes\ControllerTrait\SettingsControllerTrait;
use AppBundle\Classes\ControllerTrait\PlaceServiceControllerTrait;
use AppBundle\Entity\PaymentInfo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use AppBundle\Entity\PlaceService;


/**
 * @Route("placeservice")
 */
class PlaceServiceController extends Controller{
    use SettingsControllerTrait, PlaceServiceControllerTrait;
    
    /**
     * @Route("/list", name="placeService_list")
     */
    public function indexAction(Request $request){

        $em= $this->getDoctrine()->getManager();
        
        $filter_form= $this->createForm(PlaceServiceFilterType::class);
        $filter_form= $this->setDefaultPlace($filter_form)->setFilterAndHandleRequest($filter_form, $request);

        $criteria = [];
        $placeData = $filter_form->get('place')->getData();
        if ( !is_null($placeData) ){
            if ( !$filter_form->isSubmitted() || ( $filter_form->isSubmitted() && $filter_form->isValid() ) ) { 
                $criteria['place']= $placeData->getId();
            }        
        }

        $rPlaceService = $em->getRepository(Entity\PlaceService::class);
        $placeServices = $this->pushCumulativeCalculatorsToEnd($rPlaceService->findBy($criteria));
        
        $markForReceipt_form= $this->createForm(
                PlaceServiceMarkForReceiptCollectionType::class,
                $this->getPlaceServiceMarkForReceiptCollection($placeServices)
        )->handleRequest($request);
        if ($markForReceipt_form->isSubmitted() && $markForReceipt_form->isValid()){
            $session = $this->get('session');
            $session->set($markForReceipt_form->getName(),$markForReceipt_form->getData());
            
            return $this->redirectToRoute('receipt_prepare');
        }
        
        return $this->render('place_service/list.html.twig', [
            'placeServices'=>$placeServices,
            'filter_form'=> $filter_form->createView(),
            'mark_for_receipt_form'=> $markForReceipt_form->createView()
            ] );
    }
    
    
    private function _checksOnEditInsertAction(EntityManagerInterface $em, Form $form, PlaceService $placeService){
        
        //Check IF PaymentsInfo place equal to $placeService place
        $pInfoFormData = $form->get('paymentInfo')->getData();
        if ( !is_null($pInfoFormData) && $pInfoFormData->getPlace()->getId() !==  $form->get('place')->getData()->getId() ){
            $form->addError(new FormError('You choiced payment information from different \'Place\' than has been selected in \'Place\' field.'));
        }
        
        //Check dublication on new and edit Place && Service
        $isNew = !$em->contains($placeService);
        $isChanged = false;
        
        if ( !$isNew ){
        //If entity Changed $origPS will contain origin data otherwise (it is new) $origPS will be empty array
        $origPS = $em->getUnitOfWork()->getOriginalEntityData($placeService);
        $isChanged = $origPS['place']->getId() !== $placeService->getPlace()->getId() ||  $origPS['service']->getId() !== $placeService->getService()->getId();
        }
        
        //if row is adding or changed (submitted) then need check duplication
        if ( $isNew || $isChanged) { //then edit mode
            $tPlaceService = $em->getRepository(get_class($placeService))->findOneBy(['place'=>$form->get('place')->getData()->getId(), 'service'=>$form->get('service')->getData()->getId()]);
            if ( !is_null($tPlaceService) ){
                $form->addError(new FormError('Dublication was detected.'));
            }
        }
        return $this;
    } 


    /**
     * @Route("/{id}", name="placeService_newEdit", requirements={"id"="\d+"})
     */
    public function newEditAction(Request $request, $id = null)
    {
        $em= $this->getDoctrine()->getManager();
        
        $currentPlace = null;
        $placeService = new Entity\PlaceService();
        if ( !is_null($id) ) {
            $placeService= $em->find(Entity\PlaceService::class,$id);
            $currentPlace = $placeService->getPlace();
        }
        
        $form = $this->createForm(PlaceServiceType::class, $placeService, ['current_place'=>$currentPlace]);
        $form->handleRequest($request);
        if ( $form->isSubmitted() ) {
            $this->_checksOnEditInsertAction($em, $form, $placeService);
            if ($form->isValid() ){
                if ( !$em->contains($placeService) ) {
                    $em->persist($placeService);
                }
                $em->flush();

                return $this->redirectToRoute('placeService_list');
            }
        } else {
            if ( is_null($id) ) {
                $this->setDefaultPlace($form);
            }
        }  
        
        $fview= $form->createView();
        return $this->render('place_service/new_edit.html.twig', [
            'form' => $fview
        ]);                
        
    }


    /**
     * @Route("/delete/{id}", name="placeService_delete", requirements={"id"="\d+"})
     */
    public function deleteAction(Entity\PlaceService $placeService){
        $em= $this->getDoctrine()->getManager();
        
        $em->remove($placeService);
        $em->flush();
        
        return($this->redirectToRoute('placeService_list'));
    }

    
}
