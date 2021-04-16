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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Form\Settings\SettingsType;
use AppBundle\Form\Settings\SettingsReceiptRounderType;
use AppBundle\Classes\ReceiptRounder;
use AppBundle\Entity\Tariff;
use AppBundle\Form\Login\ConfirmPasswordType;
use AppBundle\Classes\ReceiptRounderSessionStorage;
use AppBundle\Classes\ReceiptRounderDatabaseStorage;

/**
 * Generated: Feb 13, 2019 3:22:10 PM
 * 
 * Description of SettingsController
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 * 
 * @Route("settings", name="settings_")
 * 
 */
class SettingsController extends Controller{
    //put your code here
    
    /**
     * @Route("/list", name="list")
     */
    public function indexAction(Request $request) {
        $em= $this->getDoctrine()->getManager();
        $session = $this->get('session');
        
        $settingsForm = $this->createForm(SettingsType::class);
        $settingsForm->handleRequest($request);
        if ($settingsForm->isSubmitted() && $settingsForm->isValid() ){
            $session->set($settingsForm->getName(), $settingsForm->getData()); 
            
//            $referer = $request->headers->get('referer');
//            return $this->redirect($referer);
        } else if ( $session->has($settingsForm->getName()) ){
            $sessData = $session->get($settingsForm->getName());
            if ( isset($sessData['place']) ){
                if ( !$em->contains($sessData['place'])) {
                    $sessData['place'] = $em->find(get_class($sessData['place']),$sessData['place']->getId());
                }
            }
            $settingsForm->setData($sessData);
        }
        
        $currentPlace = $settingsForm->getData()['place'];
        
        $settingsReceiptRounderForm = $this->createForm(SettingsReceiptRounderType::class,null,['entity_manager'=>$em]);
        
        //Load receipt rounder data into SettingsReceiptRounderType Form
        $receiptRounder = new ReceiptRounder($em, new ReceiptRounderDatabaseStorage($em));
        $colForm = $settingsReceiptRounderForm->get(SettingsReceiptRounderType::SETTINGS_RECEIPT_ROUNDER_COLECTION_FORM_NAME);
        $data = $receiptRounder->load($currentPlace)->getScales();
        $colForm->setData(array_values($data));
        
        $settingsReceiptRounderForm->handleRequest($request);
        if ( $settingsReceiptRounderForm->isSubmitted() && $settingsReceiptRounderForm->isValid() ){
            //change scales in session
            $receiptRounder->store($colForm->getData());
            return $this->redirectToRoute('settings_list');
        }
        
        $settingsReceiptRounderFormView = $settingsReceiptRounderForm->createView();
        
        return $this->render('settings/settings.html.twig', [
            'settingsForm'=>$settingsForm->createView(),
            'settingsReceiptRounderForm'=> $settingsReceiptRounderFormView
        ]);
    }
    
    /**
     * 
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     * 
     * @Route("/password/change", name="password_change")
     * 
     */
    public function changePasswordAction(Request $request, UserInterface $user, UserPasswordEncoderInterface $passwordEncoder) {
        
        $changePasswordForm = $this->createForm(ConfirmPasswordType::class);
        $changePasswordForm->handleRequest($request);
        if ($changePasswordForm->isSubmitted() && $changePasswordForm ->isValid()){
            
            $user->setPass( $passwordEncoder->encodePassword($user, $changePasswordForm->get('pass')->getData()) );
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash('success', 'Password was changed successfully.');
            return $this->redirectToRoute('settings_password_change');
        }
        
        return $this->render('settings/change_password.html.twig', ['changePasswordForm'=>$changePasswordForm->createView()]);
    }

}
