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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\Utils\IBANCalculatorType;
use AppBundle\Classes\MiscellaneousUtils;
use Symfony\Component\Validator\Constraints\IbanValidator;
use Symfony\Component\Validator\Constraints\Iban as IbanConstarint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Description of UtilsController
 *
 * Generated: Jan 27, 2020 11:05:49 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 * 
 * @Route("/utils", name="utils_")
 * 
 */
class UtilsController extends Controller{
    //put your code here
    
    
    /**
     * 
     * @param Request $request
     * 
     * @Route("/iban/calculator", name="iban_calculator")
     */
    public function ibanCalculatorAction( Request $request, ValidatorInterface $validator){
        
        $ibanCalculatorForm = $this->createForm(IBANCalculatorType::class);
        
        $sesion = $request->getSession();
        $fName = $ibanCalculatorForm->getName();
        if ($sesion->has($fName) ){
            $ibanCalculatorForm->setData($sesion->get($fName));
        }
        
        $errors=[];
        $iban = '';
        $ibanCalculatorForm->handleRequest($request);
        if ($ibanCalculatorForm->isSubmitted() && $ibanCalculatorForm->isValid()){
            $data = $ibanCalculatorForm->getData();
            $iban = MiscellaneousUtils::getIBAN($data['countryCode'],$data['bankCode'],$data['accountNumber'],$data['bankCodeLength'],$data['accountNumberLength'] );
            
            $errors = $validator->validate($iban, new IbanConstarint());
            if (count($errors) === 0 ){
                $sesion->set($fName, $ibanCalculatorForm->getData()) ;
            }
        }
        
        return $this->render('utils/iban.calculator.form.html.twig', ['ibanCalculatorForm'=>$ibanCalculatorForm->createView(), 'errors'=>$errors, 'iban'=>$iban]);
    }
    
}
