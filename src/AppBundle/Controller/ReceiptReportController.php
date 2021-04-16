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

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Receipt\ReceiptsForReportType;
use AppBundle\Classes\Report\ReportCollection;

use AppBundle\Classes\ControllerTrait\ReportControllerTrait;
use AppBundle\Form\Report\ReportSelectType;
use AppBundle\Classes\Report\ReportFactory;


/**
 * Description of ReceiptReportController
 *
 * Generated: Feb 10, 2020 7:10:32 AM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 * 
 * @Route("/receipt/report", name="receipt_report_")
 * 
 */
class ReceiptReportController extends Controller{
    
    use ReportControllerTrait;
    
    /**
     * 
     * @param Request $request
     * @return Response
     * 
     * @Route("/select", name="select")
     */
    public function selectReportAction(Request $request){
        $rCollection = new ReportCollection($this->container);
        $reportList = $rCollection->getReportList(ReportFactory::DEFAULT_TAG_NAME);
        $repID =  current($reportList);
        $reportSelectForm = $this->createForm(ReportSelectType::class, null, ['report_select_choices'=>$reportList]);
        
        $reportSelectForm->handleRequest($request);
        if ( $reportSelectForm->isSubmitted() && $reportSelectForm->isValid()){
            $repID = $reportSelectForm->get('reportGuid')->getData();
        }
        
        $report = $rCollection->getReport($repID);

        $selectedReceiptsForm = $this->createForm(ReceiptsForReportType::class);
        $receipts = $this->restoreReceiptsFromSession($selectedReceiptsForm->getName());
        $reportContent = $report->getView($receipts);
        if ( $reportContent instanceof Response){
            return $reportContent;
        }
                
        return $this->render('report/receipt/report_select.html.twig', [
            'reportSelectForm'=>$reportSelectForm->createView(),
            'reportContent'=>$reportContent
        ]);
    }
    
}
