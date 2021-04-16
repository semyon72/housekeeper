<?php

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

namespace AppBundle\Classes\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Description of UserPlaceFilter
 *
 * Generated: Jan 24, 2020 7:48:16 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class UserPlaceFilter extends SQLFilter {
    
    /**
     *
     * @var Collection 
     */
    protected $places = null;
    
    protected $em = null;
    
    protected $filterName = '';
    
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias) {
        if ( $targetEntity->getName() === \AppBundle\Entity\Place::class ){
            return $targetTableAlias.'.id'.$this->getSQLPlaces();
        }

        if ( in_array($targetEntity->getName(),[
            \AppBundle\Entity\Tariff::class,
            \AppBundle\Entity\Counter::class,
            \AppBundle\Entity\PlaceService::class,
            \AppBundle\Entity\PaymentInfo::class,
            \AppBundle\Entity\Scale::class ]) ){
            
            return $targetTableAlias.'.place_id'.$this->getSQLPlaces();
        }

        return '';
    }
    
    
    public function setEntityManager( EntityManagerInterface $em ) {
        $this->em = $em;
        return $this;
    }
    
    public function setPlaces( Collection $places = null ){
        $this->places = $places;
        return $this;
    }
    

    public function getPlaces(){
        return $this->places;
    }

    public function setFilterName( $filterName ){
        $this->filterName = $filterName;
        return($this);
    }

    public function getSQLPlaces(){
        $quotedPlaces  = [];
        $this->em->getFilters()->disable($this->filterName);
        try {
            //Because Collections are lazyLoaded then now will bee applyed filters for Place for UserInterface object 
            foreach ( $this->getPlaces() as $place){
                $quotedPlaces[] = $this->em->getConnection()->quote($place->getId(), \PDO::PARAM_INT);
            }
        } finally {
            $this->em->getFilters()->enable($this->filterName)
                    ->setFilterName($this->filterName)
                    ->setEntityManager($this->em)
                    ->setPlaces($this->places);
        }

        return ' IN ('. ( count($quotedPlaces) > 0 ?  implode(', ', $quotedPlaces) : ' NULL ' ).')';
    }
    
}
