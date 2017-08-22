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
            $userFields[$key] = 'wp_users.'.$userField;
        }

        $sql = $this->getUserMetaSQL($row['ID']);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $userMetaFields = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userMetaFields[] = 'wp_usermeta.'.$row['meta_key'];
        }

        return array_merge($userFields, $userMetaFields);
    }

    private function getUserSQL($fieldsToRetrieve) {

        foreach($fieldsToRetrieve as $key => $fieldToRetrieve) {
            if (strpos($fieldToRetrieve, 'wp_users.')!==0 && $fieldToRetrieve!=='*') {
                unset($fieldsToRetrieve[$key]);
            }
        }

        $fieldsSQL = implode(', ', $fieldsToRetrieve);

        $sql = 'select '.$fieldsSQL.' from wp_users';
        $sql .= ' limit ? , ?';

        return $sql;
    }

    private function getUserMetaSQL($userID, array $fieldsToRetrieve = null) {

        $sql = 'select meta_key, meta_value from wp_usermeta where ';

        $sql .= 'user_id = '.$userID;

        if ($fieldsToRetrieve) {
            
            foreach($fieldsToRetrieve as $key => $fieldToRetrieve) {
                if (strpos($fieldToRetrieve, 'wp_usermeta.')!==0) {
                    unset($fieldsToRetrieve[$key]);
                }
                $fieldsToRetrieve[$key] = str_replace('wp_usermeta.', '', $fieldToRetrieve);
            }

            $sql .= ' and ( ';
            foreach($fieldsToRetrieve as $fieldToRetrieve) {
                $sql .= ' meta_key = \''.$fieldToRetrieve.'\' or ';
            }
            $sql = substr($sql, 0, -3);
            $sql .= ' ) ';
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

        $usersSql = $this->getUserSQL($fieldsToRetrieve);

        $usersStmt = $this->pdo->prepare($usersSql);
        $this->bindLimitParameters($usersStmt, $offset, $perPage);

        $usersStmt->execute();

        $dataRows = [];

        while($usersRow = $usersStmt->fetch(PDO::FETCH_ASSOC)) {
            $dataRow = new DataRow;
            
            foreach($usersRow as $key => $value) {
                $dataRow->addDataItem(new DataItem('wp_users.'.$key, $value));
            }

            if (isset($usersRow['ID'])) {
                $userMetaSql = $this->getUserMetaSQL($usersRow['ID'], $fieldsToRetrieve);
                
                $userMetaStmt = $this->pdo->prepare($userMetaSql);
                $userMetaStmt->execute();
    
                while($userMetaRow = $userMetaStmt->fetch(PDO::FETCH_ASSOC)) {
                    $dataRow->addDataItem(new DataItem('wp_usermeta.'.$userMetaRow['meta_key'], $userMetaRow['meta_value']));
                }
            }

            $dataRows[] = $dataRow;
        }

        return $dataRows;

    }

    public function getFields() {
        return $this->fields;
    }
}