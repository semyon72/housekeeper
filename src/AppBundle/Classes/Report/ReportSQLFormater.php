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

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Description of ReportSQLFormater
 *
 * Generated: Mar 5, 2020 6:11:58 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 * 
 * It does not used yet. The reason is that Class has not a some payload for planned aims.
 * At least at this moment.
 * Some problems were not resolved.
 * 
 * Example of using:
 *
 * $reportSQLFormater = new ReportSQLFormater($em);
 *  
 * $select = $reportSQLFormater->addEntitiesInfo(['r'=>Receipt::class, 't'=>Tariff::class, 'ps'=>PlaceService::class])->getSelect();
 * 
 * $SQL = 'SELECT '.$select."\r\n".
 *     'FROM receipt r JOIN tariff t ON r.tariff_id = t.id'."\r\n".
 *     'LEFT JOIN place_service ps ON t.place_id = ps.place_id AND t.service_id = ps.service_id'."\r\n".
 *     'WHERE r.id IN ( :'.implode(', :', array_keys($ids)).') ';
 * 
 *  $rsm = $reportSQLFormater->getResultSetMapping();
 *  $query = $em->createNativeQuery($SQL,$rsm)->setParameters($ids);
 *
 *  $result = $query->getResult($query::HYDRATE_ARRAY);
 *
 *  $hRes = $reportSQLFormater->getObjectResult($result);
 * 
 */
class ReportSQLFormater {
    //put your code here
    
    
    const ALIAS_NEW_COLUMN_NAME_SEPARATOR = '_';
    
    
    /**
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em = null;
    
    
    /**
     *
     * @var array like ['Full\Entity\Class\Name'=>['meta'=>Doctrine\ORM\Mapping\ClassMetadataInfo, 'alias'=>'aliasForThisTable', 'columnAliasing'=>[originalColName=>newColName, .....] ] ] 
     */
    protected $entitiesInfo = [];
    
    /**
     *
     * @var array like ['alias'=>'correspondingClassName', ......] 
     */
    protected $aliasEntitiesInfo = [];
    
    
    
    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager; 
    }
    
    
    private function _createNewColumnName($alias, $columnName){
        return $alias.self::ALIAS_NEW_COLUMN_NAME_SEPARATOR.$columnName;
    }
    
    /**
     * 
     * @param ClassMetadataInfo $meta
     * @param string $alias
     * @param array $columnAliasing
     * @return array Array like 'meta'=>$meta, 'alias'=>$alias, 'columnAliasing'=>$columnAliasing
     */
    private function _createEntityInfo(ClassMetadataInfo $meta, $alias, array $columnAliasing){
        return [ 'meta'=>$meta, 'alias'=>$alias, 'columnAliasing'=>$columnAliasing ];
    }
    
    /**
     * If same Entity is exists then this value will be redefined.
     * 
     * @param string $entity
     * @param string|null|'' $alias
     * @return ReportSQLFormater
     * @throws \Exception
     */
    public function addEntityInfo($entity, $alias = null){
        $meta = $this->em->getClassMetadata($entity);
        
        if ( is_null($meta) ){
            throw new \Exception('Class '.$entity.' does not contain in Doctrine entity manager.');
        }
        
        if ( !$meta instanceof ClassMetadataInfo ){
            throw new \Exception('System incompatibles: For properly working ClassMetaData must be instance of Doctrine\ORM\Mapping\ClassMetadata Info.');
        }
        
        $rAlias = $meta->getTableName();
        if ( !is_null($alias) && is_string($alias) && trim($alias) !== ''  ) {
            $rAlias = $alias;
        }

        $columnAliasing = [] ; //returns table column names
        foreach ($meta->getColumnNames() as $columnName) {
            $columnAliasing[$columnName] = $this->_createNewColumnName($rAlias, $columnName);
        }

        foreach ($meta->getAssociationMappings() as $fieldName => $map) {
            //$reflFields = $meta->getReflectionProperties();
            if ( $map['isOwningSide'] ){
                $columnName = $meta->getSingleAssociationJoinColumnName($fieldName);
                $columnAliasing[$columnName] = $this->_createNewColumnName($rAlias, $columnName);
            }
        }
        
        $this->entitiesInfo[$entity] = $this->_createEntityInfo($meta, $rAlias, $columnAliasing);
        $this->aliasEntitiesInfo[$rAlias] = $entity;
        
        return $this;
    }
    
    /**
     * 
     * @return $this
     */
    public function clearEntitiesInfo(){
        $this->aliasEntitiesInfo = [];
        $this->entitiesInfo = [];
        return $this;
    }
    
    /**
     * 
     * @param array $entities Array like either ['aliasForThisTable'=>'Full\Entity\Class\Name', .....]
     *  or ['Full\Entity\Class\Name', ......] if 'aliasForThisTable' does not specified then alias same as table name. 
     * @return $this
     */
    public function addEntitiesInfo(array $entities) {
        $this->clearEntitiesInfo();
        foreach($entities as $alias=>$entityClass){
            $this->addEntityInfo($entityClass, $alias);
        }
        return $this;
    } 
    
    /**
     * 
     * @param string $tableName
     * @return string Full qualified entity's class name
     */
    public function getClassByAlias($alias){
        $result = '';
        if (isset($this->aliasEntitiesInfo[$alias])){
            $result = $this->aliasEntitiesInfo[$alias];
        }
        
        return $result;
    }


    /**
     * 
     * @param string $tableName
     * @return string Full qualified entity's class name
     */
    public function getClassByTableName($tableName){
        $result = '';
        foreach($this->entitiesInfo as $class=>$info){
            if ( $info['meta']->getTableName() === $tableName){
                $result = $class;
                break;
            }
        }
        
        return $result;
    }

    /**
     * 
     * @return string Returns Select part. 
     */
    public function getSelect($tableName = null){
        $result ='';
        
        $classes = array_keys($this->entitiesInfo);
        if ( !is_null($tableName) && is_string($tableName) && trim($tableName) !== ''  ) {
            $classes[] = $this->getClassByTableName($tableName);
        }
        
        foreach ($classes as $class){
            $classSelect=[];
            $info= $this->entitiesInfo[$class];
            foreach( $info['columnAliasing'] as $origColNAme=>$newColAlias ){
                $classSelect[] = $info['alias'].'.'.$origColNAme.' as '.$newColAlias;
            }
            $result .= implode(', ', $classSelect).", \r\n";
        }
        
        return rtrim($result,", \r\n\t");
    }
    
    /**
     * 
     * @return ResultSetMapping
     */
    public function getResultSetMapping() {
        $result = new ResultSetMapping();
        foreach ($this->entitiesInfo as $class => $info){
            foreach( $info['columnAliasing'] as $tableColName => $sqlColumnName ){
                $result->addScalarResult($sqlColumnName, $sqlColumnName);
            }
        }
        
        return $result;
    }
    
    
    /**
     * 
     * @return array
     */
    private function _getSQLColumnIndex(){
        $result = [];
        foreach ($this->entitiesInfo as $class => $info){
            $result = array_merge($result, array_fill_keys(array_values($info['columnAliasing']), $class));
        }
        return $result;
    }
    
    
    private function _getFieldBySQLColumn($sqlColumn, $colIndex){
        $result = null;
        if ( isset($colIndex[$sqlColumn]) ){
            $class = $colIndex[$sqlColumn];
            $sqlColOrigCol = array_flip($this->entitiesInfo[$class]['columnAliasing']);
            if ( isset($sqlColOrigCol[$sqlColumn]) ){
                $origCol = $sqlColOrigCol[$sqlColumn];
                $result = $this->entitiesInfo[$class]['meta']->getFieldForColumn($origCol);
            } 
        }
        return $result;
    }
    
    
    /**
     * 
     * @param array $data $data is array of rows each of them must be hydrated
     * @return array
     */
    public function getArrayResult(array $data){
        $result = [];
        
        $colIndex = $this->_getSQLColumnIndex();
        foreach ($data as $row){
            $res_row = array_intersect_key($row, $colIndex);
            if (count($res_row) !== count($row)){
                throw new \Exception('Row data does not related to configuration data.');
            }
            $resRow = [];
            foreach( $row as $sqlColumn=>$value ){
                $field = $this->_getFieldBySQLColumn($sqlColumn, $colIndex);
                $resRow[$colIndex[$sqlColumn]][$field]=$value;
            }
            $result[] = $resRow;
        } 
        
        return $result;
    }
    
    public function getObjectResult(array $data){
        $result = [];
        $arrRes = $this->getArrayResult($data);
        foreach($arrRes as $row){
            $resRow = [];
            foreach($row as $class=>$fieldValues){
                $meta = $this->entitiesInfo[$class]['meta'];
                $obj = new $class();
                foreach($fieldValues as $field => $value){
                    $meta->setFieldValue($obj, $field, $value);
                }
                $resRow[$class] = $obj;
            }
            $result[]= $resRow;
        }
        
        return($result);
    }
    
}
