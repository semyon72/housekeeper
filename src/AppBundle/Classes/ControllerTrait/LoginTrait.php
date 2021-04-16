<?php

/*
 * This file is part of the 'housekeeper' project.
 *
 * (c) Semyon Mamonov <semyon.mamonov@gmail.com>
 *
 * For the full copyright and license information of project, please view the
 * LICENSE file that was distributed with this source code. But mostly it 
 * is similar to standart MIT license that probably pointed below.
 */

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

namespace AppBundle\Classes\ControllerTrait;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use AppBundle\Entity\User;
use AppBundle\Form\Login\ConfirmEmailType;
use AppBundle\Form\Login\SignInType;

/**
 *
 * Generated: Jan 9, 2020 10:25:39 AM 
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
trait LoginTrait {
    //put your code here
    
    /**
     * 
     * @param Request $request
     * @param Form $signUpForm
     * @param UserInterface $user Checked user
     * @param string $redirectRoute route
     * @return Response For redirect into appropriate route
     */
    public function manualSecureLogin(Request $request, Form $signUpForm, UserInterface $user, $redirectRoute = ''){
        //'security.firewall.map' - Symfony\Bundle\SecurityBundle\Security\FirewallMap->getListeners($request)
        //$providerKey The provider (i.e. firewall) key
        $providerKey = $this->get('security.firewall.map')->getFirewallConfig($request)->getName();

        $request->request->add(['email'=> $signUpForm->get('email')->getData(), 'pass'=>$signUpForm->get('pass')->getData()]);

        //inspirited by https://ourcodeworld.com/articles/read/459/how-to-authenticate-login-manually-an-user-in-a-controller-with-or-without-fosuserbundle-on-symfony-3
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $authHandler = $this->get('security.authentication.guard_handler');
        //$providerKey The provider (i.e. firewall) key
        $authHandler->authenticateWithToken($token,$request,$providerKey);
        
        if ( $redirectRoute === '' ){
            $responce = $this->get('security.authentication.success_handler.for_signup')->onAuthenticationSuccess($request, $token);
        } else {
            $responce = $this->redirectToRoute($redirectRoute);
        }
        
        return $responce;
    }
    

    protected function onForgotPasswordSendMail($email, $rawPass ){
        //Swift_Mailer
        $mailer = $this->get('swiftmailer.mailer');
        $from= $this->getParameter('mailer_user');
        
        $signInForm = $this->createForm(SignInType::class,['email'=>$email,'pass'=>$rawPass]);
       
        $message = (new \Swift_Message('Housekeeper forgot password message - No need to reply.'))
                ->setFrom([$from=>'Please, do not reply'])
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        'mailer/forgot.password.html.twig',
                        [
                            'signInForm'=>$signInForm->createView(),
                        ]
                    ),
                    'text/html'
                );
        $failedRecipients = [];
        return $mailer->send($message, $failedRecipients);
    }
    
    
    protected function onSignUpSendMail(User $user){
        //Swift_Mailer
        $mailer = $this->get('swiftmailer.mailer');
        $from= $this->getParameter('mailer_user');
        $email = $user->getEmail();
        
        $confirmEmailForm = $this->createForm(ConfirmEmailType::class,['email'=>$email,'oneTimePass'=>$user->getOneTimePass()]); 
        $formView = $confirmEmailForm->createView();
        
        $message = (new \Swift_Message('Housekeeper sign up message - No need to reply.'))
                ->setFrom([$from=>'Please, do not reply'])
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                        'mailer/signup.html.twig',
                        [
                            'otpDateExp' => $user->getOtpDateExp(),
                            'confirmEmailForm'=>$confirmEmailForm->createView(),
                        ]
                    ),
                    'text/html'
                );
        $failedRecipients = [];
        return $mailer->send($message, $failedRecipients);
    }
    
    
    protected function getOneTimePass(UserInterface $user, $algorithm = 'ripemd256', $maxLength = 64, $maxRandValue = 1000000){
        return substr(hash_hmac('ripemd256', $user->getPassword().microtime().$user->getEmail().mt_rand(0,$maxRandValue),$user->getSalt()),0,$maxLength);
    }
    
    protected function initOneTimePart(User $user){
        $user->setOneTimePass( $this->getOneTimePass($user) );
        $user->setOtpDateExp( (new \DateTime())->modify('+30 min'));
        return $this;
    }
    
    protected function onSignUpInitUser(Form $signUpForm, UserPasswordEncoderInterface $passwordEncoder){
        /**
         * @var $user AppBundle\Entity\User
         */
        $user = $signUpForm->getData();
        $user->setPass($passwordEncoder->encodePassword($user, $user->getPassword()));
        $this->initOneTimePart($user);
        return $user;
    }
    
    protected function passwordGenerator($minLength = 10,
            $maxLength=16,
            $charSet = [
                'numbers'=>['set'=>'1234567890', 'min'=>2, 'max'=>4],
                'symbols'=>['set'=>'!@#$%^&*()_+=-{};:\'"/?.>,<~`|\\', 'min'=>1, 'max'=>3],
                'capitalChars'=>['set'=>'QWERTYUIOPASDFGHJKLZXCVBNM', 'min'=>1, 'max'=>3],
                'chars'=>['set'=>'qwertyuiopasdfghjklzxcvbnm', 'min'=>2, 'max'=>6]
            ]){
        
        $result = [];
        $length= mt_rand($minLength,$maxLength);
        
        $tRes = [];
        $supposedLength = 0;
        foreach ($charSet as $type=>$rule){
            $subLength =  mt_rand($rule['min'],$rule['max']);
            $tArr = str_split($rule['set']);
            shuffle($tArr);
            $tRes[$type]= array_slice($tArr, 0, $subLength);
            $supposedLength += count($tRes[$type]);
        }
        $deadloop = 100;
        $charSetKeys = array_keys($charSet);
        $fillerKey =  $charSetKeys[\count($charSetKeys)-1];
        while ($supposedLength !== $length && $deadloop > 0){
            if ( $supposedLength < $length ){
                $tArr = str_split($charSet[$fillerKey]['set']);
                shuffle($tArr);
                $padLength =  $length-$supposedLength;
                $tRes[$fillerKey] = array_merge($tRes[$fillerKey], array_slice($tArr, 0, $padLength));
                $supposedLength += $padLength;
            } else if (count($charSetKeys) > 0) {
                shuffle($charSetKeys);
                $key = $charSetKeys[0];
                $minLengthOfSet = $charSet[$key]['min'];
                if ( count($tRes[$key]) > $minLengthOfSet ){
                    $tRes[$key] = array_slice($tRes[$key], 0, -1);
                    $supposedLength--;
                } else {
                    $charSetKeys = array_slice($charSetKeys, 1);
                }
            }
            $deadloop--;
        }
        
        if ($deadloop < 1){
            throw new \Exception('Dead loop was detected during random password generation.');
        }
        
        foreach ($tRes as $charSetKey => $arrChars){
            $result = array_merge($result,$arrChars);
        }
        
        if (count($result) !== $length){
            throw new \Exception('Something went wrong during random password generation. Resulted length dosn\'t equal selected.');
        }
        
        shuffle($result);
        return implode('',$result);
    }
    
    
}
