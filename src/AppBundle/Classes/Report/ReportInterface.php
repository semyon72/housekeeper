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
 * Description of ReportInterface
 *
 * Generated: Feb 9, 2020 5:11:08 AM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
interface ReportInterface {
    
    /**
     * @return string Unique identifier of report
     */
    public static function guid();
    
    /**
     * @return string It must returns the description of report.
     */
    public static function getDescription();
    
    /**
     * 
     * @param array $receipts
     * @param PaymentInfo $paymentInfo If null then $paymentInfo will be resolved automatically
     * @return string It must return final report in any needed format [text, html, pdf etc]
     */
    public function getView(array $receipts, PaymentInfo $paymentInfo = null);
    
    /**
     * @return \AppBundle\Classes\Report\ReceiptReporter It must return instance of \AppBundle\Classes\Report\ReceiptReporter type
     */
    public function getReporter();
    
}
