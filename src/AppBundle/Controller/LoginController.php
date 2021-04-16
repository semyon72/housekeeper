<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Login\SignInType;
use AppBundle\Form\Login\SignUpType;
use AppBundle\Form\Login\ConfirmEmailType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use AppBundle\Entity\User;
use AppBundle\Classes\ControllerTrait\LoginTrait;
use AppBundle\Form\Login\ForgotPasswordType;


/**
 * @Route("/login", name="login_")
 */
class LoginController extends Controller
{
    use LoginTrait;
    
    /**
     * @Route("/signin", name="signin")
     */
    public function signInAction( AuthenticationUtils $authenticationUtils, Request $request )
    {
        
//        $authenticationUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->createForm(SignInType::class, [
            'email' => $lastUsername,
        ]);
    
        return $this->render(
            'login/signin.form.html.twig',
            array(
                'signInForm' => $form->createView(),
                'error' => $error,
            )
        );        
        
//        
//         // get the login error if there is one
//        $error = $authenticationUtils->getLastAuthenticationError();
//        
//        
//        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        //$user = $this->getUser();        
//        
//
//        $lastUser = $authenticationUtils->getLastUsername();        
//        
//
//        
//        $signinForm = $this->createForm(SignInType::class);
//        
//        $signinForm->handleRequest($request);
//        if ( $signinForm->isSubmitted() && $signinForm->isValid()  ){
//            
//            
//            //return $this->redirectToRoute('place_list');
//        }
//        
//        return $this->render('login/signin.form.html.twig', array(
//            'signInForm'=> $signinForm->createView()
//        ));
    }
    
    
    /**
     * @Route("/signout", name="signout")
     */
    public function signOutAction( AuthenticationUtils $authenticationUtils, Request $request )
    {
        throw new \Exception('It can\'t be reached anyway. It is logout action.');
    }    
    
    /**
     * @Route("/signup", name="signup")
     */
    public function signUpAction( Request $request, UserPasswordEncoderInterface $passwordEncoder )
    {
        $signUpForm = $this->createForm(SignUpType::class);
        $signUpForm->handleRequest($request);
        if ( $signUpForm->isSubmitted() && $signUpForm->isValid()  ){
            $em = $this->getDoctrine()->getManager();
            $email= $signUpForm->get('email')->getData();
            $user = $em->getRepository(User::class)->findOneBy(['email'=>$email]);
            if ($user !== null){
                if ( $passwordEncoder->isPasswordValid($user, $signUpForm->get('pass')->getData() ) ){
                    if ( $user->getEmailIsConfirmed() ) {
                        //manually login
                        return $this->manualSecureLogin($request,$signUpForm,$user);
                        //Checked
                    } else {
                        //need to reinit one time password part
                        //and to resend email 
                        $this->initOneTimePart($user); 
                        //Checked
                    }
                } else {
                    //Password is wrong and this email address is not acceptable for using.
                    //Probably, need to use the forgot password link.
                    $signUpForm->addError(new FormError('Email address <'.$email.'> is busy already. If you forgot password then use appropriate link below.'));
                    //Checked
                }
            } else {
                //New user
                $user = $this->onSignUpInitUser($signUpForm,$passwordEncoder);
                //Checked
            }
            
            if ( $signUpForm->isValid() ){
                if ( $this->onSignUpSendMail($user) > 0 ){
                    //store data
                    if ( !$em->contains($user) ){
                        $em->persist($user);
                    }
                    $em->flush();

                    return $this->render('login/signup.success.html.twig',['email'=> $email]);
                } else {
                    $signUpForm->addError( new FormError('Email address <'.$to.'> unreachable.') );
                }
            }
        }
        
        return $this->render('login/signup.form.html.twig', array(
            'signUpForm'=> $signUpForm->createView()
        ));
    }

    
    /**
     * 
     * @Route("/confirm/email", name="confirm_email")
     *  
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmEmailAction(Request $request){
        $confirmForm = $this->createForm(ConfirmEmailType::class);
        
        $confirmForm->handleRequest($request);
        if( $confirmForm->isSubmitted() &&  $confirmForm->isValid() ){
            
            $em = $this->getDoctrine()->getManager();
            $email = $confirmForm->get('email')->getData();
            $user = $em->getRepository(User::class)
                        ->loadUserForEmailConfirmation(
                            $email,
                            $confirmForm->get('oneTimePass')->getData()
                        );
            if ($user !== null){
                $user->setEmailIsConfirmed(true);
                $em->flush();
                
                return $this->render('login/confirm.email.success.html.twig',['email'=>$email]);
            }
            $confirmForm->addError(new FormError('The data that was sent is not relevant.'));
            
        }
        
        return $this->render('login/confirm.email.form.html.twig', ['confirmForm'=>$confirmForm->createView()]);
    }

    
    /**
     * @Route("/forgot/password", name="forgot_password")
     */
    public function forgotPasswordAction( Request $request, UserPasswordEncoderInterface $passwordEncoder )
    {
        $forgotPasswordForm = $this->createForm(ForgotPasswordType::class);
        $forgotPasswordForm->handleRequest($request);
        if ($forgotPasswordForm->isSubmitted() && $forgotPasswordForm->isValid()){
            $email = $forgotPasswordForm->get('email')->getData();
            
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(['email'=>$email]);
            if ( !is_null($user) ){
                $newPass = $this->passwordGenerator();
                //'forgot.password.html'
                if ( $this->onForgotPasswordSendMail($email, $newPass) > 0 ){
                    //store data
                    $user->setPass($passwordEncoder->encodePassword($user, $newPass));
                    $em->flush();

                    return $this->render('login/forgot.password.success.html.twig',['email'=>$email]);
                }
                $forgotPasswordForm->addError( new FormError('Email address <'.$email.'> unreachable.') );
            } else { 
                $forgotPasswordForm->addError(new FormError('Something went wrong! Seems you made a typo in the email.'));
            }
        }
        
        return $this->render('login/forgot.password.form.html.twig',['forgotPasswordForm'=>$forgotPasswordForm->createView()]);
    }
    
    

}
