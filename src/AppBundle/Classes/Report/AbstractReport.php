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

namespace AppBundle\Classes\Report;

use AppBundle\Entity\PaymentInfo;

/**
 * Description of AbstractReport
 *
 * Generated: Feb 13, 2020 6:26:09 AM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
abstract class AbstractReport implements ReportInterface {
    
    /**
     *
     * @var ReceiptReporter 
     */
    protected $reporter = null;

    protected $templateEngine = null;
    
    protected $template = '';
    
    
    public function __construct(ReceiptReporter $receiptReporter, $templateEngine, $template = '') {
        $this->templateEngine= $templateEngine;
        if ( $template !== '' ){
            $this->template = $template;
        }
        $this->reporter = $receiptReporter;
    }
    
    protected function renderView(array $parameters){
        return $this->templateEngine->render($this->template,$parameters);
    }
    
    /**
     * 
     * @return ReceiptReporter
     */
    public function getReporter() {
        return $this->reporter;
    }

    
    abstract public function getView(array $receipts, PaymentInfo $paymentInfo = null); 
}
