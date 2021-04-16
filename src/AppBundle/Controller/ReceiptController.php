<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

use AppBundle\Form\Filter\ReceiptFilterType;
use AppBundle\Form\PlaceService\PlaceServiceMarkForReceiptCollectionType;
use AppBundle\Form\PlaceService\PlaceServiceConfirmToRecieptType;
use AppBundle\Form\Receipt\ReceiptType;
use AppBundle\Form\Receipt\ReceiptsForReportType;
use AppBundle\Form\Settings\SettingsReceiptRounderType;

use AppBundle\Entity;
use AppBundle\Classes\Calculator\PlaceServiceCalculatorFactory;
use AppBundle\Classes\ControllerTrait\SettingsControllerTrait;
use AppBundle\Classes\ReceiptRounder;
use AppBundle\Classes\ReceiptRounderDatabaseStorage;
use AppBundle\Classes\ControllerTrait\ReportControllerTrait;

/**
 * 
 * @Route("receipt", name="receipt_")
 */
class ReceiptController extends Controller {
    use SettingsControllerTrait, ReportControllerTrait;
    
    /**
     * @Route("/list", name="list")
     */
    public function indexAction(Request $request){
        $em= $this->getDoctrine()->getManager();
        
        $filter_form= $this->createForm(ReceiptFilterType::class);
        $filter_form= $this->setDefaultPlace($filter_form)->setFilterAndHandleRequest($filter_form);
        
        $criteria = array_merge(['dateB'=>null,'dateE'=>null,'place'=>null,'service'=>null],(array)$filter_form->getData());
        $receipts= $em->getRepository(Entity\Receipt::class)->findFiltered($criteria);
        $receiptsForReportForm = $this->createForm(ReceiptsForReportType::class, null, ['receipts_data'=>$receipts]);
        
        $receiptsForReportForm->handleRequest($request);
        if ( $receiptsForReportForm->isSubmitted() && $receiptsForReportForm->isValid() ){
            $receipts = $receiptsForReportForm->get('receipt')->getData();
            $this->storeReceiptsToSession($receiptsForReportForm->getName(), $receipts->toArray());
            
            return $this->redirectToRoute('receipt_report_select');
        }
        
        return $this->render( 'receipt/list.html.twig',[ 
            'receiptsForReportForm'=>$receiptsForReportForm->createView(),
            'filter_form'=> $filter_form->createView()]
            );
    }
    
    /**
     * @Route("/{id}", name="newEdit", requirements={"id"="\d+"}, methods={"GET","POST","PUT"})
     */
    public function newEditAction(Request $request, $id = null){
        $em= $this->getDoctrine()->getManager();
        
        $receipt = new Entity\Receipt();
        if ( !is_null($id) ) {
            $receipt= $em->find(Entity\Receipt::class,$id);
        } 
        
        $form = $this->createForm(ReceiptType::class, $receipt, ['entity_manager'=>$em]);
        $form->handleRequest($request);  
        if ( $form->isSubmitted() && $form->isValid() ){
            
            $em->persist($receipt);
            $em->flush();

            return $this->redirectToRoute('receipt_list');
            }
        
        $fview= $form->createView();
        return $this->render('receipt/new_edit.html.twig', [
            'form' => $fview
        ]);                
        
    }


    /**
     * @Route("/delete/{id}", name="delete", requirements={"id"="\d+"}, methods={"GET","POST","DELETE"})
     */
    public function deleteAction(Entity\Receipt $receipt){
        $em= $this->getDoctrine()->getManager();
        
        if ( !$em->getRepository(get_class($receipt))->hasChildren($receipt->getId()) ) {
            $em->remove($receipt);
            $em->flush();
        }

        return($this->redirectToRoute('receipt_list'));
    }
    
    
    /**
     * @Route("/prepare", name="prepare")
     */
    public function prepareAction(Request $request){
        $formNameOfEntries = 'placeServiceMarkForReceipt';
        
        
        $sess = $this->get('session');
        //it is need only for form's name resolving
        $preparedEntriesForm= $this->createForm(
            PlaceServiceMarkForReceiptCollectionType::class
        );
        
        $sessAttrName = $preparedEntriesForm->getName();
        $preparedEntries = [];
        if ( $sess->has($sessAttrName) ){
            $preparedEntries= $sess->get($sessAttrName);
            if ( isset($preparedEntries[$formNameOfEntries]) && is_array($preparedEntries[$formNameOfEntries]) ){
                $preparedEntries = $preparedEntries[$formNameOfEntries];
            }
        }
        
        $preparedEntry = [];
        $pcEntryForm = $this->createForm(PlaceServiceConfirmToRecieptType::class);
        $pcEntryForm->handleRequest($request);
        if ($pcEntryForm->isSubmitted() && $pcEntryForm->isValid()){
            $preparedEntry = $pcEntryForm->getData()['placeServeiceEntry'];
            if ( $pcEntryForm->get('skip')->isClicked() ){
                array_push( $preparedEntries, $preparedEntry );
                $sess->set($sessAttrName,["$formNameOfEntries"=>$preparedEntries]);
                //move processed entry to end because was selected 'skip' action 
                return $this->redirectToRoute('receipt_prepare');
            }
            
            //'confirm' is pushed
            //Check Reciept Counter data for marked Place Service entry.
            
        } else {
            if ( !is_array($preparedEntries) ) {
                throw new \Exception ('Session attribute ['.$sessAttrName.']['.$formNameOfEntries.'] has not array type or was been not right transformed.');
            }

            //clear the didn't marked rows 
            $preparedEntries = array_filter($preparedEntries, function($value){
                return $value['mark'] == true; 
                
            });

            if( count($preparedEntries) === 0 ){
                return $this->redirectToRoute('receipt_list');
            }
            
            $preparedEntry = array_shift($preparedEntries);
            $pcEntryForm->setData(['placeServeiceEntry'=>$preparedEntry]);
            if( count($preparedEntries) === 0 ) {
                $sess->remove($sessAttrName);
            } else  { 
                $sess->set($sessAttrName,["$formNameOfEntries"=>$preparedEntries]);
            }
            //Then
            //Check Reciept Counter data for marked Place Service entry.            
        }

        //Checking Reciept Counter data for marked Place Service entry.        
        if( !is_null($preparedEntry) && is_array($preparedEntry) ){
            
            //Checking
            $entityManager = $this->getDoctrine()->getManager();
            $calculator = PlaceServiceCalculatorFactory::getCalculatorInstance($entityManager, (int)$preparedEntry['service']);
            $calculator->setPlaceService((int)$preparedEntry['place'],(int)$preparedEntry['service'])->handle();
            
            //$calculator->getResult()['receipt_new'] need rounding using configuration rules.
            $newReceipt = $calculator->getResult()['receipt_new'];
            if(!is_null($newReceipt)){
                //Here we do the rounding of data (Value and Total) follow by rules stored in session for certain tariff-id
                //@todo It needs to remake for the using data for rounding from the database.  
                $reseiptRounder = new ReceiptRounder($entityManager);
                $reseiptRounder->loadWithSession(SettingsReceiptRounderType::SETTINGS_RECEIPT_ROUNDER_COLECTION_FORM_NAME, $sess, $newReceipt->getTariff()->getPlace());
                $reseiptRounder->roundReceipt($newReceipt);
            }
            
            if ( $calculator->isHandled() && $calculator->isValid() ) {
                if( $pcEntryForm->isSubmitted() ){
                    
                    //Save data into receipt
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($newReceipt);
                    $em->flush();
                    
                    return $this->redirectToRoute('receipt_prepare');
                }
            } else {
                if( $pcEntryForm->isSubmitted() ){
                    //If at submit stage was detected error then to restore session's data
                    //and start from begin (redirect at itself).
                    array_unshift( $preparedEntries, $pcEntryForm->getData()['placeServeiceEntry'] );
                    $sess->set($sessAttrName,["$formNameOfEntries"=>$preparedEntries]);
                    return $this->redirectToRoute('receipt_prepare');
                } else {
                    $pcEntryForm->remove('confirm');
                }
            }

        } else throw new \Exception ('Ooops ... Something went wrong. Prepared entry is null or not array.');
        
        return $this->render('receipt/prepare.html.twig', array(
            'preparedEntries'=>$preparedEntries,
            'placeServiceEntry'=>$pcEntryForm->getData(),
            'calculator'=>$calculator,            
            'placeServiceEntryForm'=>$pcEntryForm->createView()   
        ));
    }
    
    /**
     * 
     * @Route("/createFromCounter/{counterId}", name="createFromCounter", requirements={"counterId"="\d+"} )
     * @param Request $request
     */
    public function createFromCounterAction(Request $request, $counterId) {
        $em= $this->getDoctrine()->getManager();
        
        $counter = $em->find(Entity\Counter::class, $counterId);
        $receipt = new Entity\Receipt();
        $dateE = clone $counter->getOnDate();
        $dateE->modify('noon');
        $dateB = clone $dateE;
        
        $receipt->setDateB($dateB)
                ->setDateE($dateE)
                ->setValueB($counter->getValue() - 0.01)
                ->setValueE($counter->getValue())
                ->setValue(0.01);

        $criteria = [
                'currentDate'=>$receipt->getDateE(),
                'place'=>$counter->getPlace()->getId(),
                'service'=>$counter->getService()->getId()
            ];
        $probTariff = $em->getRepository(Entity\Tariff::class)->findActiveBy($criteria,null,1);
        if ( count($probTariff) > 0) {
            $receipt->setTariff($probTariff[0]['tariff']);
            $totalCalc = (new MultiTariffTotalCalculator())->setScale(4)
                    ->setValue($receipt->getValue())
                    ->setDefaultItem($probTariff[0]['tariff']->getUnitValue())
                    ->setItems($probTariff[0]['tariff']->getTariffValues());
            $calcError = $totalCalc->getItems()->getLastError();
            if( $calcError !== ''){
                throw new \Exception($calcError);
            }
            $receipt->setTotal( $totalCalc->calculate() );            
        }
        
        //Here we do the rounding of data (Value and Total) follow by rules stored in session for certain tariff-id
        ( new ReceiptRounder($em, new ReceiptRounderDatabaseStorage($em)) )->load( $receipt->getTariff()->getPlace() )->roundReceipt($receipt);
        
        $form = $this->createForm(ReceiptType::class, $receipt, ['entity_manager'=>$em, 'action'=>$this->generateUrl('receipt_newEdit')]);
        
        if ( count($probTariff) === 0) {
            $serviceName = $counter->getService()->getName();
            $form->get('tariff')->addError(new FormError('Pay attention - appropriate tariff is absent for this data of counter. Counter had service - "'.$serviceName.'"' ));
            $form->get('total')->addError(new FormError('Total was not calculated because appropriate tariff is absent.'));
        }
        
        $fview= $form->createView();
        return $this->render('receipt/new_edit.html.twig', [
            'form' => $fview
        ]);                
        
    }
    
}
