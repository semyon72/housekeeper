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

namespace AppBundle\Classes;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Scale;
use AppBundle\Entity\Place;
use AppBundle\Entity\Service;

/**
 * Description of ReceiptRounderSessionStorage
 *
 * Generated: Feb 12, 2020 1:13:40 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ReceiptRounderSessionStorage implements ReceiptRounderStorageInterface {

    const STORAGE_KEY = 'receipt_rounder_session_storage';
    
    /**
     *
     * @var SessionInterface 
     */
    protected $session = null;
    
    /**
     *
     * @var EntityManagerInterface 
     */
    protected $em = null;
    
    
    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager) {
        $this->session = $session;
        $this->em = $entityManager;
    }
    
    /**
     * 
     * @param Scale $scale
     * @return string
     */
    protected function getSessionScaleKey(Scale $scale){
        return $scale->getPlace()->getId().'&'.$scale->getService()->getId();
    }
    
    /**
     * 
     * @param mixed $criteria
     * @return array $data Collection of rows like [ 'key' => ['placeId'=>placeId, 'serviceId'=>serviceId, 'valueScale'=>x, 'totalScale'=>y ], 'key' => [....], ..... ] 
     */
    public function load($criteria = null) {
        $result = [];
        $data = $this->session->get(self::STORAGE_KEY);
        if ( is_array($data) ){
            foreach($data as $key=>$scaleFormatedRow){
                if (!is_null($criteria) && is_a($criteria, Place::class) && 
                    $criteria->getId() !==  $scaleFormatedRow['placeId']){
                    continue;
                }
                $scale = new Scale();
                $place= $this->em->getRepository(Place::class)->findOneBy(['id'=>$scaleFormatedRow['placeId']]);
                $scale->setPlace($place);
                $service= $this->em->getRepository(Service::class)->findOneBy(['id'=>$scaleFormatedRow['serviceId']]);
                $scale->setService($service);
                $scale->setTotalScale($scaleFormatedRow['totalScale']);
                $scale->setvalueScale($scaleFormatedRow['valueScale']);
                $result[$this->getSessionScaleKey($scale)] = $scale;
            }
        }    
        return $result;
    }

    protected function getStoreFormatedRow(Scale $scale){
        return [
            'placeId'=>$scale->getPlace()->getId(),
            'serviceId'=>$scale->getService()->getId(),
            'valueScale'=>$scale->getValueScale(),
            'totalScale'=>$scale->getTotalScale()
        ];
    }
    
    /**
     * @param array $data  Collection of Scales
     * 
     * @return $this
     * @throws \Exception
     */
    public function store($data) {
        $sessData=$this->session->get(self::STORAGE_KEY);
        foreach ($data as $scale) {
            $key = $this->getSessionScaleKey($scale);
            $sessData[$key]= $this->getStoreFormatedRow($scale);
        }
        $this->session->set(self::STORAGE_KEY, $sessData);

        return $this; 
    }
    
    
}
