<?php

/*
 * The MIT License
 *
 * Copyright 2020 Semyon Mamonov <semyon.mamonov@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\PaymentInfo\PaymentInfoType;
use AppBundle\Entity\PaymentInfo;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\PaymentInfo\PaymentInfoJustPlacePriorType;

/**
 * Description of PaymentInfoController
 *
 * Generated: Jan 26, 2020 9:40:10 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 * 
 * @Route("/payment/info", name="payment_info_")
 */
class PaymentInfoController extends Controller{
    //put your code here
    
    
    private function throwBadRequestByIdException($id){
        throw new BadRequestHttpException('Payment information with Id - \''.$id.'\' doesn\'t found.');        
    }
    
    /**
     * 
     * @param Request $resuest
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @Route("/list", name="list", methods={"GET"})
     * 
     */
    public function indexAction(Request $resuest){
        $paymentInfos = $this->getDoctrine()
                            ->getManager()
                            ->getRepository(PaymentInfo::class)
                            ->findBy([],['place'=>'ASC','priority'=>'DESC']);
        
        $paymentInfoList=[];
        foreach($paymentInfos as $paymentInfo){
            $id = $paymentInfo->getId();
            $paymentInfoList[$id]['paymentInfo'] = $paymentInfo;
            $paymentInfoList[$id]['shortPaymentInfoForm'] = $this->createForm(PaymentInfoJustPlacePriorType::class, $paymentInfo)->createView();
        }
        
        return $this->render('payment_info/list.html.twig', ['paymentInfoList'=> $paymentInfoList]);
    }
    
    
    /**
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @Route("/{id}/change/placepriority", name="changePlacePriority", methods={"POST"}, requirements={"id"="\d+"})
     * 
     */
    public function changePlacePriorityAction(Request $request, $id){
        $em =  $this->getDoctrine()->getManager();
        $paymentInfo =  $em->getRepository(PaymentInfo::class)->findOneBy(['id'=>$id]);
        if ($paymentInfo === null){
            $this->throwBadRequestByIdException($id);
        }
        
        $paymentInfoForm = $this->createForm(PaymentInfoJustPlacePriorType::class, $paymentInfo);
        $paymentInfoForm->handleRequest($request);
        if ( $paymentInfoForm->isSubmitted() && $paymentInfoForm->isValid() ){
            $em->persist($paymentInfo);
            $em->flush();
        }
        
        return $this->redirectToRoute('payment_info_list');
    }
    
    
    /**
     * @Route("/{id}/delete/", name="delete", requirements={"id"="\d+"})
     */
    public function deleteAction(PaymentInfo $paymentInfo){
        $em= $this->getDoctrine()->getManager();
        
        if ( !$em->getRepository(get_class($paymentInfo))->hasChildren($paymentInfo->getId()) ) {
            $em->remove($paymentInfo);
            $em->flush();
        }
        
        return $this->redirectToRoute('payment_info_list');
    }
    
    
    /**
     * 
     * @param Request $request
     * @param UserInterface $user
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @Route("/{id}", name="newEdit", methods={"POST","GET"}, requirements={"id"="\d+"})
     * 
     */
    public function newEditAction(Request $request, UserInterface $user, $id = null){
        $em = $this->getDoctrine()->getManager();
        $paymentInfo = null;
        if ( !is_null($id) ){
            $paymentInfo = $em->getRepository(PaymentInfo::class)->findOneBy(['id'=>$id]);
            if ($paymentInfo === null){
                $this->throwBadRequestByIdException($id);
            }
        }
        
        if ($paymentInfo === null){
            $paymentInfo = new PaymentInfo();
            $paymentInfo->setUser($user);
        }
        
        $paymentInfoForm = $this->createForm(PaymentInfoType::class, $paymentInfo);
        $paymentInfoForm->handleRequest($request);
        if ( $paymentInfoForm->isSubmitted() && $paymentInfoForm->isValid() ){
            if ( !$em->contains($paymentInfo) ){
                $em->persist($paymentInfo);
            }
            $em->flush();
            
            return $this->redirectToRoute('payment_info_list');
        }
        
        return $this->render('payment_info/new_edit.html.twig', ['paymentInfoForm'=> $paymentInfoForm->createView()]);
    }
    
}
