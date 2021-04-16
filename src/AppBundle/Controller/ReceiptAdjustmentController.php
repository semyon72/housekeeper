<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

use AppBundle\Form\PlaceService\PlaceServiceMarkForReceiptCollectionType;
use AppBundle\Form\PlaceService\PlaceServiceConfirmToRecieptType;
use AppBundle\Form\Receipt\ReceiptType;
use AppBundle\Form\Receipt\ReceiptsForReportType;
use AppBundle\Form\Settings\SettingsReceiptRounderType;

use AppBundle\Classes\Calculator\PlaceServiceCalculatorFactory;
use AppBundle\Classes\ControllerTrait\SettingsControllerTrait;
use AppBundle\Classes\ReceiptRounder;
use AppBundle\Classes\ReceiptRounderDatabaseStorage;
use AppBundle\Classes\ControllerTrait\ReportControllerTrait;
use AppBundle\Entity\ReceiptAdjustment;
use AppBundle\Form\Filter\ReceiptAdjustmentFilterType;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\ReceiptAdjustment\ReceiptAdjustmentType;
use AppBundle\Entity\Receipt;
use AppBundle\Form\ReceiptAdjustment\ReceiptAdjustmentsForReportType;
use AppBundle\Classes\AdjustmentCalculator;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Description of ReceiptAdjustmentController
 *
 * Generated: Mar 09, 2020 11:03:24 AM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 * 
 * @Route("receipt/adjustment", name="receipt_adjustment_")
 */
class ReceiptAdjustmentController extends Controller {
    use SettingsControllerTrait, ReportControllerTrait;
    
    /**
     * @Route("/list", name="list")
     */
    public function indexAction(Request $request){
        $em= $this->getDoctrine()->getManager();
        
        $filter_form= $this->createForm(ReceiptAdjustmentFilterType::class);
        $filter_form= $this->setDefaultPlace($filter_form)->setFilterAndHandleRequest($filter_form);
        
        $criteria = array_merge(['dateB'=>null,'dateE'=>null,'place'=>null,'service'=>null],(array)$filter_form->getData());
        $receiptAdjustments= $em->getRepository(ReceiptAdjustment::class)->findFiltered($criteria);
        
//  Only to printing the adjustments of receipts. 
        $receiptAdjustmentsForReportForm = $this->createForm(ReceiptAdjustmentsForReportType::class, null, ['receipt_adjustments_data'=>$receiptAdjustments]);
        $receiptAdjustmentsForReportForm->handleRequest($request);
        if ( $receiptAdjustmentsForReportForm->isSubmitted() && $receiptAdjustmentsForReportForm->isValid() ){
            $receiptAdjustments = $receiptAdjustmentsForReportForm->get('adjustments')->getData();
            $this->storeReceiptsToSession($receiptAdjustmentsForReportForm->getName(), $receiptAdjustments->toArray());
            
            return $this->redirectToRoute('receipt_adjustment_report_select');
        }
        
        return $this->render( 'receipt_adjustment/list.html.twig',[ 
            'receiptAdjustmentsForReportForm'=>$receiptAdjustmentsForReportForm->createView(),
            'filter_form'=> $filter_form->createView()]
            );
    }

    
    /**
     * 
     * @param Form $receiptAdjustmentForm
     * @return Response
     */
    private function _autoCalculateAdjustmentResponse(Form $receiptAdjustmentForm, Request $request = null) {
        $result = null;
        
        if ( !$receiptAdjustmentForm->isSubmitted() ){
            return $result;
        }
        
        $tariffForm = $receiptAdjustmentForm->get('tariff');
        if ( $receiptAdjustmentForm->get('calculateForTariff')->isClicked() && $tariffForm->isValid() ){
            $proposedTariff =  $tariffForm->getData();
            if ( !is_null($proposedTariff) ){
                $em = $this->getDoctrine()->getManager();
                $calculator = new AdjustmentCalculator($em);
                $adjustment= $receiptAdjustmentForm->getData();
                $calculator->calculateAdjustment($adjustment, $proposedTariff);
                
                $resultForm = $this->createForm(ReceiptAdjustmentType::class, $adjustment, ['current_receipt'=>$adjustment->getReceipt()]);
                        
                $result= $this->render('receipt_adjustment/new_edit.html.twig', [
                    'form' => $resultForm->createView()
                ]);                
            }
        }
        
        return $result;
    }
    
    
    /**
     * 
     * @param Request $request
     * @param integer $receiptId Receipt id
     * @return Response
     * @throws Exception
     * 
     * @Route("/new/{receiptId}", name="new", requirements={"receiptId"="\d+"}, methods={"GET","POST","PUT"})
     * 
     */
    public function newAction(Request $request, $receiptId){
        $em= $this->getDoctrine()->getManager();
        
        $receipt = $em->find(Receipt::class,$receiptId);
        if ( is_null($receipt) ){
            throw new $this->createNotFoundException('Throughout create/edit action the Receipt with ID: '.$receiptId.' doesn\'t found.');
        }
        
        $adjustment = new ReceiptAdjustment();
        $form = $this->createForm(ReceiptAdjustmentType::class, $adjustment->setReceipt($receipt), ['current_receipt'=>$receipt ]);

        $form->handleRequest($request);
        if ( $form->isSubmitted() ){
            
            $autoCalculatedAdjustmentResponse = $this->_autoCalculateAdjustmentResponse($form, $request); 
            if ( !is_null($autoCalculatedAdjustmentResponse) && is_a($autoCalculatedAdjustmentResponse, Response::class)){
                return $autoCalculatedAdjustmentResponse;
            }
            
            if ( $form->isValid() ){
                $em->persist($adjustment);
                $em->flush();

                return $this->redirectToRoute('receipt_list');
            }
        }
        
        return $this->render('receipt_adjustment/new_edit.html.twig', [
            'form' => $form->createView()
        ]);                
        
    }

    
    
    /**
     * 
     * @param Request $request
     * @param integer $adjustmentId
     * @return Response
     * @throws Exception
     * 
     * @Route("/edit/{adjustmentId}", name="edit", requirements={"adjustmentId"="\d+"}, methods={"GET","POST","PUT"})
     * 
     */
    public function editAction(Request $request, $adjustmentId){
        $em= $this->getDoctrine()->getManager();
        
        $adjustment = $em->find(ReceiptAdjustment::class,$adjustmentId);
        if ( is_null($adjustment) ){
            throw new $this->createNotFoundException('Throughout create/edit action the Receipt with ID: '.$adjustmentId.' doesn\'t found.');
        }
        
        $currentReceipt = $adjustment->getReceipt();
        
        $form = $this->createForm(ReceiptAdjustmentType::class, $adjustment, ['current_receipt'=>$currentReceipt]);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ){
            $em->flush();
            return $this->redirectToRoute('receipt_adjustment_list');
        }
        
        $fview= $form->createView();
        return $this->render('receipt_adjustment/new_edit.html.twig', [
            'form' => $fview
        ]);                
        
    }


    /**
     * @Route("/delete/{adjustmentId}", name="delete", requirements={"adjustmentId"="\d+"}, methods={"GET","POST","DELETE"})
     */
    public function deleteAction($adjustmentId){
        $em= $this->getDoctrine()->getManager();
        
        $adjustment = $em->find(ReceiptAdjustment::class, $adjustmentId);
        if ( is_null($adjustment) ){
            throw $this->createNotFoundException('Adjustment with ID: "'.$adjustmentId.'" doesn\'t found.'); 
        } 
        
        $em->remove($adjustment);
        $em->flush();
        
        return($this->redirectToRoute('receipt_list'));
    }

}
