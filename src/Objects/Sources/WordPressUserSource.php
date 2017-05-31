<?php

namespace RapidWeb\uxdm\Objects\Sources;

use RapidWeb\uxdm\Interfaces\SourceInterface;
use RapidWeb\uxdm\Objects\DataRow;
use RapidWeb\uxdm\Objects\DataItem;
use PDO;
use PDOStatement;
use Exception;

class WordPressUserSource implements SourceInterface
{
    private $pdo;
    private $fields = [];
    private $userType;

    public function __construct(PDO $pdo) {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
        $this->fields = $this->getUserFields();
    }

    private function getUserFields() {
        $sql = $this->getUserSQL(['*']);
        
        $stmt = $this->pdo->prepare($sql);
        $this->bindLimitParameters($stmt, 0, 1);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $userFields = array_keys($row);

        foreach($userFields as $key => $userField) {
            $userFields[$key] = 'user.'.$userField;
        }

        $sql = $this->getUserMetaSQL($row['ID']);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $userMetaFields = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userMetaFields[] = 'user_meta.'.$row['meta_key'];
        }

        return array_merge($userFields, $userMetaFields);
    }

    private function getUserSQL($fieldsToRetrieve) {

        $fieldsSQL = implode(', ', $fieldsToRetrieve);

        $sql = 'select '.$this->fieldsSQL.' from wp_users ';
        $sql .= ' limit ? , ?';

        return $sql;
    }

    private function getUserMetaSQL($userID, array $fieldsToRetrieve = null) {

        $sql = 'select meta_key, meta_value from wp_usermeta where ';

        $sql .= 'user_id = '.$userID;

        if ($fieldsToRetrieve) {
            $sql .= ' and ( ';
            foreach($fieldsToRetrieve as $fieldToRetrieve) {
                ' meta_key = \''.$fieldsToRetrieve.'\' or ';
            }
            $sql = substr($sql, 0, -3);
            $sql .= ' ) ';
        }

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
                $dataRow->addDataItem(new DataItem('user.'.$key, $value));
            }

            $sql = $this->getUserMetaSQL($row['ID'], $fieldsToRetrieve);

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $userMetaFields = [];

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dataRow->addDataItem(new DataItem('user_meta.'.$row['meta_key'], $row['meta_value']));
            }

            $dataRows[] = $dataRow;
        }

        return $dataRows;

    }

    public function getFields() {
        return $this->fields;
    }
}