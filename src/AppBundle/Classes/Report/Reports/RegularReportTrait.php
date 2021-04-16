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

namespace AppBundle\Classes\Report\Reports;

use AppBundle\Entity\PlaceService;
use AppBundle\Entity\PaymentInfo;
use AppBundle\Entity\Receipt;

/**
 * Description of RegularReportTrait
 *
 * Generated: Mar 7, 2020 6:21:15 AM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
trait RegularReportTrait {
    //put your code here
    
    
    /**
     * Split array receipts into array like [
     *    'PaymentinfoId' => [
     *      'paymentInfo'=> PaymentInfoObject,
     *      'YEARMONTH'=>[
     *          0=>ReceiptObject,
     *          1=>ReceiptObject,
     *          .....
     *      ],
     *    'PaymentinfoId' => [
     *      'paymentInfo'=> PaymentInfoObject,
     *      '202001'=>[
     *          0=>ReceiptObject,
     *          1=>ReceiptObject,
     *          .....
     *      ]
     *     ........
     * ]
     * 
     * @param array $receipts
     * @return array
     */
    public function stripeToDiferentPlaces(array $receipts){
        $stripedReceipts= [];
        if (count($receipts) > 0){

            $em= $this->getReporter()->getEntityManager();
            $rPaymentInfo = $em->getRepository(PaymentInfo::class);
            
            $qb = $em->createQueryBuilder()->select('ps','pi')->from(PlaceService::class, 'ps')->leftJoin('ps.paymentInfo', 'pi');
            $psQuery = $qb->where('ps.service = :service and ps.place = :place')->getQuery();
                    
            foreach($receipts as $receipt){
                $place = $receipt->getTariff()->getPlace();
                $monthYear = $receipt->getDateE()->format('Ym');
                $paymentInfo = null;
                $placeService = $psQuery->setParameters(['place'=>$place->getId(), 'service'=>$receipt->getTariff()->getService()->getId()])->getSingleResult();
                if ($placeService !== null && !is_null($placeService->getPaymentInfo()) ){
                    $paymentInfo = $placeService->getPaymentInfo() ;
                } else {
                    $pInfos = $rPaymentInfo->findBy(['place'=>$place->getId()],['priority'=>'DESC'], 1);
                    if (count($pInfos) > 0){
                        $paymentInfo = $pInfos[0];
                    } else {
                        throw new \Exception('Receipt must have atleast one payment Information.');
                    }
                }

                $paymentInfoId = $paymentInfo->getId();
                if ( !isset($stripedReceipts[$paymentInfoId]['paymentInfo']) ){
                    $stripedReceipts[$paymentInfoId]['paymentInfo'] = $paymentInfo;
                }
                $stripedReceipts[$paymentInfoId][$monthYear]['receipts'][] = $receipt;
            }
        }        
        
        return $stripedReceipts;
    }
    
    
}
