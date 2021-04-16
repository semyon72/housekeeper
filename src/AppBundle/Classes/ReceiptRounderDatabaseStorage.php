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

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Tariff;
use AppBundle\Entity\Place;
use AppBundle\Entity\Scale;

/**
 * Description of ReceiptRounderDatabaseStorage
 *
 * Generated: Feb 17, 2020 3:48:15 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ReceiptRounderDatabaseStorage  implements ReceiptRounderStorageInterface {
    
    protected $entityManager = null;


    public function __construct(EntityManagerInterface $em) {
        $this->entityManager = $em;
    }
    
    /**
     * 
     * @param Place|array|null $criteria
     */
    public function load($criteria = null) {
        $tCriteria = [];
        if ( !is_null($criteria) ){
            if (is_array($criteria) ){
                $tCriteria = $criteria;
            } else if(is_object($criteria) && is_a($criteria, Place::class)){
                $tCriteria = ['place'=>$criteria->getId()];
            }
        }
        return $this->entityManager->getRepository(Scale::class)->findBy($tCriteria);
    }

    /**
     * 
     * @param array $data Array of Scale objects
     * @throws \Exception
     */
    public function store($data) {
        foreach ( $data as $key=>$score){
            if ( !$this->entityManager->contains($score) ){
                $placeId = $score->getPlace()->getId();
                $serviceId = $score->getService()->getId();
                if ( !is_null($this->entityManager->getRepository(Scale::class)->findOneBy(['place'=>$placeId, 'service'=>$serviceId])) ){
                    throw new \Exception('Something went wrong - seems we have logic mistake. Now, we try to store duplicate of Scale.');
                }
                $this->entityManager->persist($score) ; 
            }
        }
        $this->entityManager->flush();
    }

}
