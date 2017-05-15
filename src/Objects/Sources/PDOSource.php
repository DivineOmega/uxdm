<?php

namespace RapidWeb\uxdm\Objects\Sources;

use RapidWeb\uxdm\Interfaces\SourceInterface;
use RapidWeb\uxdm\Objects\DataRow;
use RapidWeb\uxdm\Objects\DataItem;
use PDO;
use PDOStatement;
use Exception;

class PDOSource implements SourceInterface
{
    private $pdo;
    private $tableName;
    private $fields = [];
    private $overrideSQL;

    public function __construct(PDO $pdo, $tableName) {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->fields = $this->getTableFields();
    }

    private function getTableFields() {
        $sql = $this->getSQL(['*']);
        
        $stmt = $this->pdo->prepare($sql);
        $this->bindLimitParameters($stmt, 0, 1);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $tableFields = array_keys($row);
        return $tableFields;
    }

    public function setOverrideSQL($overrideSQL) {

        $selectString = 'SELECT ';

        if (stripos($overrideSQL, $selectString)===false) {
            throw new Exception('PDO Source Override SQL must contain \''.$selectString.'\' to select source data.');
        }

        $limitString = 'LIMIT ? , ?';

        if (stripos($overrideSQL, $limitString)===false) {
            throw new Exception('PDO Source Override SQL must contain \''.$limitString.'\' to allow for pagination of source data.');
        }

        $this->overrideSQL = $overrideSQL;
        $this->fields = $this->getTableFields();
    }

    private function getSQL($fieldsToRetrieve) {

        $fieldsSQL = implode(', ', $fieldsToRetrieve);

        $sql = 'select '.$fieldsSQL.' from '.$this->tableName.' limit ? , ?';

        if ($this->overrideSQL) {
            $sql = $this->overrideSQL;
        }
        return $sql;
    }

    private function bindLimitParameters(PDOStatement $stmt, $offset, $perPage) {
        $stmt->bindValue(1, $offset, PDO::PARAM_INT);
        $stmt->bindValue(2, $perPage, PDO::PARAM_INT);
    }

    public function getDataRows($page = 1, $fieldsToRetrieve = []) {

        $perPage = 10;

        $offset = (($page-1) * $perPage);

        $sql = $this->getSQL($fieldsToRetrieve);
        
        $stmt = $this->pdo->prepare($sql);
        $this->bindLimitParameters($stmt, $offset, $perPage);

        $stmt->execute();

        $dataRows = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dataRow = new DataRow;
            
            foreach($row as $key => $value) {
                $dataRow->addDataItem(new DataItem($key, $value));
            }

            $dataRows[] = $dataRow;
        }

        return $dataRows;

    }

    public function getFields() {
        return $this->fields;
    }
}