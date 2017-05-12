<?php

namespace RapidWeb\uxdm\Objects\Sources;

use RapidWeb\uxdm\Interfaces\SourceInterface;
use RapidWeb\uxdm\Objects\DataRow;
use RapidWeb\uxdm\Objects\DataItem;
use PDO;

class PDOSource implements SourceInterface
{
    private $pdo;
    private $tableName;
    private $fields = [];

    public function __construct(PDO $pdo, $tableName) {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->fields = $this->getTableFields();
    }

    private function getTableFields() {
        $stmt = $this->pdo->prepare('DESCRIBE '.$this->tableName);
        $stmt->execute();
        $tableFields = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $tableFields;
    }

    public function getDataRows($page = 1, $fieldsToRetrieve = []) {

        $perPage = 10;

        $offset = (($page-1) * $perPage);

        $fieldsSQL = implode(', ', $fieldsToRetrieve);
        
        $sql = 'select '.$fieldsSQL.' from '.$this->tableName.' limit '.$offset.', '.$perPage;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $dataRows = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dataRow = new DataRow;
            
            foreach($row as $key => $value) {
                $dataItem = new DataItem;
                $dataItem->fieldName = $key;
                $dataItem->value = $value;
                $dataRow->addDataItem($dataItem);
            }

            $dataRows[] = $dataRow;
        }

        return $dataRows;

    }

    public function getFields() {
        return $this->fields;
    }
}