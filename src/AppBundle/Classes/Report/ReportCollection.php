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

use AppBundle\Classes\Report\Reports\Kharkiv;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Classes\ReceiptRounder;
use AppBundle\Classes\ReceiptRounderSessionStorage;
use AppBundle\Classes\ReceiptRounderDatabaseStorage;

/**
 * Description of ReportCollection
 *
 * Generated: Feb 11, 2020 4:50:28 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ReportCollection {

    /**
     *
     * @var ReportFactory 
     */
    protected $container = null; 
    
    /**
     *
     * @var twig|templating 
     */
    protected $templateEngine = null; 

    /**
     *
     * @var EntityManagerInterface 
     */
    protected $entityMananger = null; 
    
    /**
     *
     * @var ReceiptRounder 
     */
    protected $receiptRounder = null;
    
    /**
     *
     * @var ReportFactory 
     */
    protected $reportFactory = null; 
    

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        
        if ($this->container->has('templating')) {
            $this->templateEngine = $this->container->get('templating');
        } elseif ($this->container->has('twig')) {
            $this->templateEngine = $this->container->get('twig');
        } else {
            throw new \LogicException('You can not use the "render" method if the Templating Component or the Twig Bundle are not available. Try running "composer require symfony/twig-bundle".');
        }        

        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application. Try running "composer require symfony/orm-pack".');
        }
        $this->entityMananger = $this->container->get('doctrine')->getManager();
        
        if (!$this->container->has('session')) {
            throw new \LogicException('You can not use the "session" mechanism if the Session Component are not available.');
        }

        $this->receiptRounder = new ReceiptRounder($this->entityMananger, new ReceiptRounderDatabaseStorage($this->entityMananger));
        
        $this->reportFactory = new ReportFactory();
        $this->initReports($this->reportFactory);
    }

    /**
     * @todo Need to do abstract for public using
     */
    protected function initReports(ReportFactory $reportFactory ){
        $reportFactory->add(Kharkiv\RegularReportWithAdjustment::class);
        $reportFactory->add(Kharkiv\RegularReport::class);
        $reportFactory->add(Kharkiv\RegularReportPDF::class);
        $reportFactory->add(Kharkiv\RegularAdjustmentReport::class,'adjustment');        
    }
    
    /**
     * 
     * @param string $tag Value is same to what you added inside of self::initReports() method. 
     * @return array List of known reports
     */
    public function getReportList($tag = null){
        $result = [];
        foreach($this->reportFactory->getReportList($tag) as $class ){
            $guid = $this->reportFactory->getReportGuid($class);
            $result[$this->reportFactory->getReportName($guid)] = $guid;
        }
        return $result;
    }
    
    /**
     * 
     * @param string $guid
     * @param string $template
     * @return ReportInterface
     */
    public function getReport($guid, $template = ''){
        $report = $this->reportFactory->getReport($guid,false);
        if ( !is_object($report)){
            $rReporter = new ReceiptReporter($this->entityMananger, $this->receiptRounder);
            $report = $this->reportFactory->getReport($guid, true, [$rReporter, $this->templateEngine, $template]);
        }
        return $report;
    }
    
}
