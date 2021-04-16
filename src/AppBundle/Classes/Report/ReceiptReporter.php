<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Report;

use AppBundle\Entity\Receipt;
use AppBundle\Classes\ReceiptRounder;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\PaymentInfo;

/**
 * Description of Reporter
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ReceiptReporter {
    
    protected $entities = [];
        
    /**
     *
     * @var EntityManagerInterface 
     */
    protected $entityManager = null;
    
    /**
     *
     * @var PaymentInfo 
     */
    protected $paymentInfo = null;
    
    /**
     *
     * @var ReceiptRounder 
     */
    protected $receiptRounder = null;
    

    public function __construct( EntityManagerInterface $entityManager, ReceiptRounder $receiptRounder, PaymentInfo $paymentInfo = null ) {
        $this->entityManager = $entityManager;
        $this->receiptRounder = $receiptRounder;
        if ($paymentInfo !== null){
            $this->setPaymentInfo($paymenntInfo);
        }
    }    
    
    /**
     * 
     * @return EntityManagerInterface
     */
    public function getEntityManager() {
        return $this->entityManager;
    }
    
    /**
     * It sets $paymenntInfo object and display it on internal structures.
     * If data of PaymentInfo will be changed then need to refresh internal data
     * structures and it possible through refreshPaymentInfo() method.
     * 
     * @param PaymentInfo $paymenntInfo
     * @return $this Description
     */
    public function setPaymentInfo( PaymentInfo $paymenntInfo ){
        $tPaymInfo = $this->paymentInfo;
        $this->paymentInfo = $paymenntInfo;
        if ( is_null($tPaymInfo) || $this->paymentInfo->getPlace()->getId() !==  $paymenntInfo->getPlace()->getId() ){ 
            $this->receiptRounder->load($this->paymentInfo->getPlace());
        }
        
        return $this;
    }
    
    /**
     * 
     * @return PaymentInfo
     */
    public function getPaymentInfo(){
        return $this->paymentInfo;
    }
    
    /**
     * 
     * @return ReceiptRounder
     */
    public function getReceiptRounder(){
       return $this->receiptRounder;
    }
    
    public function setEntities(array $entities) {
        $this->entities = [];
        foreach ( $entities as $entity ){
            if ($entity instanceof ReportReceiptEntity){
                $this->entities[$entity->getReceipt()->getId()]= $entity;
            }
        }
        return $this;
    }
    
    public function getEntities() {
        return $this->entities;
    }
    
    /**
     * Reset internal structures into start state.
     * 
     * @return $this
     */
    public function reset(){
        $this->paymentInfo = null;
        $this->entities = [];
        
        return $this;
    }
    
    
    /**
     * Add Receipt object into list
     * If $receipt has duplicate then It will throw exception if command chaining is using.
     * Because function will return null if duplication is detected.
     * 
     * @param Receipt $receipt
     * @return $this
     * @throws Exception
     */
    public function addReceipt(Receipt $receipt) {
        
        if (is_null($this->paymentInfo )){
            //to find appropriate for reciept place
            $rPaymentInfo = $this->entityManager->getRepository(PaymentInfo::class);
            $paymentInfo = $rPaymentInfo->findBy(['place'=>$receipt->getTariff()->getPlace()],['priority'=>'DESC'], 1);
            if ( count($paymentInfo) === 0 ){
                throw new \Exception('Payment information that intended to current receipt [id => \''.$receipt->getId().'\'] does not found.');
            }
            $this->setPaymentInfo($paymentInfo[0]);
        }
        
        if ( $this->paymentInfo->getPlace()->getId() !== $receipt->getTariff()->getPlace()->getId() ){
            throw new \Exception('Receipt does not indeed for current payment information [Receipt.id => \''.$receipt->getId().'\', PaymentInfo.id => \''.$this->paymentInfo->getId().'\']');
        }
        
        if ( !isset($this->entities[$receipt->getId()]) ){
            $entity = new ReportReceiptEntity($receipt,$this->receiptRounder);
            $this->entities[$entity->getReceipt()->getId()]= $entity;
        }
        
        return $this;
    }
    
    public function getReportName(){
        $result='Report-'. date('Y-m-d H:i:s');
        
        if( count($this->entities) > 0 ){
            $entity = $this->entities[key($this->entities)];
            $result .= '-['.$entity->getReceipt()->getTariff()->getPlace()->getName().']';
            $result .= '-['.$entity->getMonth().'-'.$entity->getYear().']';
        }
        
        return strtr($result,'<>:"/\|?*\'','__________');
    }

    public function getInternalAccountNumber(){
        return $this->getCode();
    }
    
    public function getCode(){
        return $this->paymentInfo->getCode();
    }

    public function getFirstName() {
        return $this->paymentInfo->getFirstName();
    }

    public function getSecondName() {
        return $this->paymentInfo->getSecondName();
    }
    
    public function getLastName() {
        return $this->paymentInfo->getLastName();
    }
    
    public function getFullName($order='FLS', $splitter='   '){
        $result = trim($this->getFirstName().$splitter.$this->getLastName().$splitter.$this->getSecondName());
        $order = strtoupper(trim($order));
        $order = array_combine(str_split($order), array_fill(0, strlen($order), '') );
        $func = ['F'=>'getFirstName', 'L'=>'getLastName', 'S'=>'getSecondName'];
        if ( count(array_intersect_key($func, $order))  > 0 ){
            $result = '';
            foreach ( $order as $key=>$value ){
                if(isset($func[$key])){
                    $result .= call_user_func([$this,$func[$key]]).$splitter;
                }
            }
            $result= substr($result,0,-strlen($splitter));
        }
        return $result;
    }

    public function getCountry() {
        return $this->paymentInfo->getCountry();
    }
    

    public function getRegion() {
        return $this->paymentInfo->getRegion();
    }
    
    public function getCity() {
        return $this->paymentInfo->getCity();
    }
    
    public function getStreet() {
        return $this->paymentInfo->getStreet();
    }
    
    public function getHouse() {
        return $this->paymentInfo->getHouse();
    }
    
    public function getApartment() {
        return $this->paymentInfo->getApartment();
    }
    
    public function getIban() {
        return $this->paymentInfo->getIban();
    }
    
    public function getBankCode() {
        return $this->getSubIban();
    }

    public function getSubIban() {
        return $this->paymentInfo->getSubIban();
    }

    public function getNote() {
        return $this->paymentInfo->getNote();
    }
    
}
