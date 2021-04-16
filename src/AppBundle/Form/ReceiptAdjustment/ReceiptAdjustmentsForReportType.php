<?php

/*
 *  This file is part of the 'housekeeper' project.
 * 
 *  (c) Semyon Mamonov <semyon.mamonov@gmail.com>
 * 
 *  For the full copyright and license information of project, please view the
 *  LICENSE file that was distributed with this source code. But mostly it 
 *  is similar to standart MIT license that probably pointed below.
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

namespace AppBundle\Form\ReceiptAdjustment;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use AppBundle\Entity\ReceiptAdjustment;

/**
 * Description of ReceiptAdjustmentsForReportType
 *
 * Generated: Mar 09, 2020 11:19:35 AM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ReceiptAdjustmentsForReportType extends AbstractType{
    //put your code here
    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('adjustments', EntityType::class, [
                'class' => ReceiptAdjustment::class,
                'expanded'=>'true',
                'multiple'=>'true',
                'choices'=> $options['receipt_adjustments_data'],
                //false - label will contain '' but null will set 'id' as content of lable
                'choice_label'=>function($choice, $key, $value){ return false; },  
                'allow_extra_fields'=>true,
                'choice_attr'=>function($choiceValue, $key, $value) {
                                    return ['checked' => 'checked'];
                                },
            ]);
    }
    
    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver) {
        //$resolver->setRequired('receipt_adjustments_data');
        $resolver->setDefault('receipt_adjustments_data', []);
    }
    
    public function getBlockPrefix() {
        return parent::getBlockPrefix();
    }
    
    
}
