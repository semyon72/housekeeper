<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Report\Reports\Kharkiv;

use AppBundle\Classes\Report\ReportInterface;
use AppBundle\Classes\Report\ReceiptReporter;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Classes\Report\Reports\RegularReportTrait;

/**
 * Description of RegularReportPDF
 *
 * @author semyon
 */
class RegularReportPDF implements ReportInterface{
    
    use RegularReportTrait;

    /**
     *
     * @var Reporter 
     */
    protected $reporter = null;
    
    protected $FPDF = null;
    
    
    public function __construct(ReceiptReporter $receiptReporter, $templateEngine, $template) {
        $this->reporter = $receiptReporter;
        $this->FPDF = new \FPDF();
    }
    

    public function CreateReport(){

        $startMainDataSetX = 61;
        $colWidth = ['serviceName'=>45,'month'=>7,'year'=>7,'total'=>17,'valueEnd'=>16.5,'valueBegin'=>16.5,'valueDiff'=>15,'tariff'=>10];
        $rowHeight = 6;
        
        $this->FPDF->Line(28, 28, 194, 28);        

        $this->FPDF->SetFont('times', 'bi', 9);
        $this->FPDF->SetXY(41, 30);
        $this->FPDF->Write(2.4, mb_convert_encoding('Повідомл.','cp1251','utf-8'));
        
        $this->FPDF->SetFont('times', 'bd', 9);
        $this->FPDF->SetXY(92, 31);
        $this->FPDF->Write(2.4, mb_convert_encoding('ОПЛАТА ЗА КОМУНАЛЬНІ ПОСЛУГИ','cp1251','utf-8'));

        $this->FPDF->SetXY($startMainDataSetX, 34);
//        $this->FPDF->Write(3.1, mb_convert_encoding('ВАТ "Мегабанк" рахунок одержувача 290231 МФО 351629 ЄДРПОУ 09804119','cp1251','utf-8'));
        $this->FPDF->Write(3.1, mb_convert_encoding('Рахунок одержувача IBAN: '.
                            $this->reporter->getIban().' ЄДРПОУ: '.
                            $this->reporter->getBankCode(),'cp1251','utf-8'));
//New main data row >>> Personal account data
        $this->FPDF->SetFont('times', '', 9);
        $this->FPDF->SetXY($startMainDataSetX, 39);
        $this->FPDF->Write(3.1, mb_convert_encoding('Особистий рахунок','cp1251','utf-8'));
        $this->FPDF->Rect(91, 38, 52, 5);
        $this->FPDF->SetFont('times', '', 11);
        $this->FPDF->SetXY(93, 39.5);
        $this->FPDF->Write(2.2, mb_convert_encoding(implode(' . ', str_split($this->reporter->getInternalAccountNumber())),'cp1251','utf-8'));
//New main data row >>> Name
        $this->FPDF->SetFont('times', '', 9);
        $this->FPDF->SetXY($startMainDataSetX, 45);
        $this->FPDF->Write(3.1, mb_convert_encoding('Прізвище, ім\'я, по батькові','cp1251','utf-8'));
        $this->FPDF->Line(100, 48, 183, 48);        
        $this->FPDF->SetFont('times', 'i', 11);
        $this->FPDF->SetXY(101, 46);
        $this->FPDF->Write(0, mb_convert_encoding($this->reporter->getFullName('LFS'),'cp1251','utf-8'));
//New main data row >>> Address
        $this->FPDF->SetFont('times', '', 9);
        $this->FPDF->SetXY($startMainDataSetX, 49);
        $this->FPDF->Write(3.1, mb_convert_encoding('Адреса: вул.','cp1251','utf-8'));
        $this->FPDF->Line(81, 52, 134, 52);        
        $this->FPDF->SetFont('times', 'i', 11);
        $this->FPDF->SetXY(91, 50);
        $this->FPDF->Write(0, mb_convert_encoding($this->reporter->getStreet(),'cp1251','utf-8'));
        
        $this->FPDF->SetFont('times', '', 9);
        $this->FPDF->SetXY(137, 49);
        $this->FPDF->Write(3.1, mb_convert_encoding('Буд.','cp1251','utf-8'));
        $this->FPDF->Line(145, 52, 159, 52);        
        $this->FPDF->SetFont('times', 'i', 11);
        $this->FPDF->SetXY(149, 50);
        $this->FPDF->Write(0, mb_convert_encoding($this->reporter->getHouse(),'cp1251','utf-8'));
        
        $this->FPDF->SetFont('times', '', 9);
        $this->FPDF->SetXY(161, 49);
        $this->FPDF->Write(3.1, mb_convert_encoding('Кв.','cp1251','utf-8'));
        $this->FPDF->Line(170, 52, 183, 52);        
        $this->FPDF->SetFont('times', 'i', 11);
        $this->FPDF->SetXY(171, 50);
        $this->FPDF->Write(0, mb_convert_encoding($this->reporter->getApartment(),'cp1251','utf-8'));
        
//New main data row >>> Bonus
        $this->FPDF->SetFont('times', '', 9);
        $this->FPDF->SetXY($startMainDataSetX, $this->FPDF->GetY()+3);
        $this->FPDF->Write(3.1, mb_convert_encoding('Пільга, %','cp1251','utf-8'));

//New main data row >>> Header
        $this->FPDF->SetXY($startMainDataSetX, $this->FPDF->GetY()+4);
        $this->FPDF->SetFont('times', '', 9);
        $this->FPDF->Cell($colWidth['serviceName'], $rowHeight, mb_convert_encoding('Вид платежу','cp1251','utf-8'),1,0,'C');
        $this->FPDF->Cell($colWidth['month'], $rowHeight, mb_convert_encoding('Міс.','cp1251','utf-8'),1,0,'C');
        $this->FPDF->Cell($colWidth['year'], $rowHeight, mb_convert_encoding('Рік','cp1251','utf-8'),1,0,'C');
        $this->FPDF->Cell($colWidth['total'], $rowHeight, mb_convert_encoding('Сума','cp1251','utf-8'),1,0,'C');
        $this->FPDF->SetFont('times', '', 8);        
        $this->FPDF->Cell($colWidth['valueBegin']+$colWidth['valueEnd'], $rowHeight/2, mb_convert_encoding('Показання лічильників','cp1251','utf-8'),1,0,'C');
        $curX = $this->FPDF->GetX();
        $curY = $this->FPDF->GetY();
        $this->FPDF->SetXY($curX-33, $curY+3);
        $this->FPDF->Cell($colWidth['valueBegin'], $rowHeight/2, mb_convert_encoding('кінцеві','cp1251','utf-8'),1,0,'C');
        $this->FPDF->Cell($colWidth['valueEnd'], $rowHeight/2, mb_convert_encoding('початкові','cp1251','utf-8'),1,0,'C');
        $this->FPDF->SetXY($curX, $curY);
        $this->FPDF->SetFont('times', '', 9);
        $this->FPDF->Cell($colWidth['valueDiff'], $rowHeight, mb_convert_encoding('Різн.','cp1251','utf-8'),1,0,'C');
        $this->FPDF->Cell($colWidth['tariff'], $rowHeight, mb_convert_encoding('Тариф','cp1251','utf-8'),1,2,'C');

        $lastKey= array_slice(array_keys($colWidth),-1)[0];
        $totalAmount = 0;
        foreach ($this->reporter->getEntities() as $entityId => $receiptEntity){
//New main data rows            
            $totalAmount += $receiptEntity->getTotal();
            $this->FPDF->SetX($startMainDataSetX);
            foreach($colWidth as $cellName=>$cellWidth){
                $curPos = 0; // at end of line, if 1 at start of next line
                if ($lastKey === $cellName) $curPos=1;
                $this->FPDF->Cell($cellWidth, $rowHeight, mb_convert_encoding($receiptEntity->{'get'.ucfirst($cellName)}(),'cp1251','utf-8'),1,$curPos,'C');
            }
        }        

//New main data row >>> Total & Sign
        $curY = $this->FPDF->GetY();
        $this->FPDF->SetFont('times', '', 8);
        $this->FPDF->SetXY($startMainDataSetX, $curY+5);
        $this->FPDF->Write(3.1, mb_convert_encoding('Усього ','cp1251','utf-8'));
        $this->FPDF->SetFont('times', 'bd', 9);
        $this->FPDF->SetXY($startMainDataSetX+17,$curY+5);
        $this->FPDF->Write(3.1, mb_convert_encoding("$totalAmount",'cp1251','utf-8'));
        $this->FPDF->Line($startMainDataSetX+11, $curY+8, 133, $curY+8);
        
        $this->FPDF->SetFont('times', 'bd', 8);
        $this->FPDF->SetXY(135, $curY+5);
        $this->FPDF->Write(3.1, mb_convert_encoding('Підпис платника','cp1251','utf-8'));
        $this->FPDF->Line(160, $curY+8, 193, $curY+8);
        $this->FPDF->Ln();
        
        $curX = $this->FPDF->GetX(); 
        $curY = $this->FPDF->GetY();
        
        $this->FPDF->SetFont('times', 'bi', 9);
        $this->FPDF->SetXY(41, $curY-17);
        $this->FPDF->Write(2.4, mb_convert_encoding('Касир','cp1251','utf-8'));
        
        //drawing left side vertical line & bottom horizontal line
        $this->FPDF->SetFont('times', 'bi', 9);
        $this->FPDF->SetXY($curX, $curY);
        $this->FPDF->Line(58, 28, 58, $this->FPDF->GetY()+3);        
        $this->FPDF->Line(28, $this->FPDF->GetY()+3, 194, $this->FPDF->GetY()+3);        
        
    }

    /**
     * 
     * @return \AppBundle\Classes\Report\ReceiptReporter
     */
    public function getReporter() {
        return $this->reporter;
    }

    public function getView(array $receipts, \AppBundle\Entity\PaymentInfo $paymentInfo = null) {
        //FIlter places into different parts
        $stripedReceipts = $this->stripeToDiferentPlaces($receipts);

        $resultView = '';
        foreach ($stripedReceipts as $paymentInfoId => $rInfoByMonth){
            $cPaymentInfo = $rInfoByMonth['paymentInfo'];
            $cReceipts = array_diff_key($rInfoByMonth, ['paymentInfo'=>1]);
            foreach ($cReceipts as $yearMonth=>$receiptsInfo){
                $this->reporter->reset();
                if (!is_null($cPaymentInfo)){
                    $this->reporter->setPaymentInfo($cPaymentInfo);
                } elseif ($paymentInfo !== null ) {
                    $this->reporter->setPaymentInfo($paymentInfo);
                }

                foreach($receiptsInfo['receipts'] as $receipt){
                    $this->reporter->addReceipt($receipt);
                }

                $this->FPDF->AddPage();
                $this->CreateReport();
            }
        }
        
        return new Response($this->FPDF->Output('',$this->reporter->getReportName().'.pdf',true));
    }

    public static function getDescription() {
        return "Regular report of consume the communal services in Kharkiv - PDF format.";        
    }

    /**
     * It possible to create at https://www.guidgenerator.com/online-guid-generator.aspx
     * @return string
     */
    public static function guid() {
        return '24da1f20-08a1-455e-aeaf-cb16383dc44c';
    }

}
