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

namespace AppBundle\Classes\Doctrine;

use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Description of RepositoryTrait
 *
 * Generated: Mar 21, 2020 3:20:24 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
trait RepositoryTrait {
    
    /**
     * Returns True or False if it has or not children respectively or null if result undefined. 
     * 
     * At Mar 21, 2020 3:20:24 PM it has had next dependencies
     *   `service` - `tariff`, `place_service`, `counter`, `service_parameter`, `scale`
     *   `place` - `counter`, `tariff`, `place_service`, `user_place`, `payment_info`, `scale`
     *   `tariff` - `receipt`, `receipt_adjustment`, `tariff_value`
     *   `receipt` - `receipt_adjustment`
     *   `user` - `user_place`, `payment_info`
     *   `payment_info` - `place_service`        
     * 
     * @param string|integer $id 
     * @param array $tables Array like ['tariff'=>'service_id', 'place_service'=>'service_id', 'counter'=>'service_id', 'service_parameter'=>'service_id', 'scale'=>'service_id']
     * @param boolean $throwException If true then will be thrown exception if result will be undefined.
     * @return null|boolean True or False if it has or not children respectively or null if result undefined. 
     * @throws Exception
     */
    protected function _hasChildren($id, array $tables, $throwException = true ){
        $result = null;
        $SQL = '';
        
        $rsm = new ResultSetMapping();
        
        foreach ($tables as $table=>$keyField){
            $resultColumnName = $table.'_count';
            $rsm->addScalarResult($resultColumnName, $resultColumnName);
            $SQL .= "( SELECT COUNT(*) FROM (SELECT ${keyField} FROM ${table} as ${table} WHERE ${table}.${keyField} = :id LIMIT 1) as ${table}_ ) as ${resultColumnName}, \r\n";
        }
        if ( strlen(trim($SQL)) > 0 ){
            $SQL = 'SELECT '.rtrim($SQL,", \r\n\t");
            $query = $this->getEntityManager()->createNativeQuery($SQL, $rsm)->setParameter('id', $id);
            $resSQL = $query->getSQL();
            $arrRes= $query->getScalarResult();
            if ( count( $arrRes ) > 0 ){
                $result = array_sum($arrRes[0]) > 0; 
            }
        }
        
        if ($throwException === true && is_null($result)){
            throw new Exception('Something went wrong. Result musn\'t be undefined.'); 
        }

        return $result;
    }
    
    
}
