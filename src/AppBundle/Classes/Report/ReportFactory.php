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

/**
 * Description of ReportFactory
 *
 * Generated: Feb 9, 2020 5:28:32 AM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ReportFactory {
    
    const DEFAULT_TAG_NAME = '';

    protected $reportList = [];
    
    protected $guidReference = [];
    
    /**
     *
     * @var array Array of tags like [AdjustmentsRepot::class=>'adjustments', .... ]  
     */
    protected $tagRefrence = [];
    
    
    /**
     * Add to list 
     * 
     * @param mixed $class Class or object must be instance of ReportInterface
     * @return $this
     * @throws \Exception
     */
    public function add($class, $tag = self::DEFAULT_TAG_NAME){
        $className = $class;
        $instance = null;
        if (is_object($class)){
            $instance = $class;
            $className = get_class($class);
        }
        
        if ( !is_subclass_of($className, ReportInterface::class) ){
            throw new \Exception('Class must be instance of \''.ReportInterface::class.'\' interface.');
        }
        
        $this->reportList[$className] = $instance;
        $this->guidReference[$className::guid()] = $className;
        $this->tagRefrence[$className] = $tag;
        
        return $this;
    }
    
    /**
     * It will do lazy load and return same instance in future.
     * But If $instantiate is false and class was not instantiated then
     * it will return only class name.
     * 
     * @param string $guid
     * @param boolean $instantiate
     * @return \AppBundle\Classes\Report\ReportInterface|string|null
     */
    public function getReport($guid, $instantiate = true, $parameters = []){
        $result = null;
        if ( isset($this->guidReference[$guid]) ){
            $className = $this->guidReference[$guid];
            $result = $this->reportList[$className];
            if ( is_null($result) ){
                $result = $className;
                if ( $instantiate ) {
                    $refl = new \ReflectionClass($className);
                    $result = $refl->newInstanceArgs($parameters);
                    $this->reportList[$className] = $result;
                }
            } 
        }
        return $result;
    }

    /**
     * 
     * @param string $guid
     * @return string
     */
    public function getReportName($guid){
        return $this->getReportNameByClass($this->getReport($guid,false));
    }

    /**
     * 
     * @param string $guid
     * @return string
     */
    public function getReportNameByClass($class){
        $result= '';
        if ( key_exists($class, $this->reportList) ){
            $result = $class::getDescription();
        }
        return($result);
    }
    
    /**
     * 
     * Returns report's classes that in list already.
     * You can get guid or description each of them to call static method 
     * AppBundle\Classes\Report\ReportInterface::guid() or
     * AppBundle\Classes\Report\ReportInterface::getDescription()
     * 
     * @param string|null $tag If null will be returned full list.
     * @return array It returns array of \AppBundle\Classes\Report\ReportInterface
     */
    public function getReportList($tag = null){
        $result = array_keys($this->reportList);
        if ( !is_null($tag) ){
            $tRes=[];
            foreach($result as $class){
                if ( isset($this->tagRefrence[$class]) && $this->tagRefrence[$class] === $tag ){
                    $tRes[] = $class;
                }
            }
            $result = $tRes;
        }
        return $result;
    }
    
    /**
     * 
     * @param string $class
     * @return string
     */
    public function getReportGuid($class){
        $result= '';
        if ( key_exists($class, $this->reportList) ){
            $result = $class::guid();
        }
        return($result);
    }
    
}
