<?php

namespace RapidWeb\uxdm\Objects\Sources;

use RapidWeb\uxdm\Interfaces\SourceInterface;
use RapidWeb\uxdm\Objects\DataRow;
use RapidWeb\uxdm\Objects\DataItem;
use PDO;
use PDOStatement;
use Exception;

class WordPressSource implements SourceInterface
{
    private $pdo;
    private $fields = [];
    private $postType;

    public function __construct(PDO $pdo, $postType = 'post') {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
        $this->postType = $postType;
        $this->fields = $this->getPostFields();
    }

    private function getPostFields() {
        $sql = $this->getPostSQL(['*']);
        
        $stmt = $this->pdo->prepare($sql);
        $this->bindLimitParameters($stmt, 0, 1);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $postFields = array_keys($row);

        foreach($postFields as $key => $postField) {
            $postFields[$key] = 'post.'.$postField;
        }

        $sql = $this->getPostMetaSQL($row['ID']);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $postMetaFields = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $postMetaFields[] = 'post_meta.'.$row['meta_key'];
        }

        return array_merge($postFields, $postMetaFields);
    }

    private function getPostSQL($fieldsToRetrieve) {

        $fieldsSQL = implode(', ', $fieldsToRetrieve);

        $sql = 'select '.$this->fieldsSQL.' from wp_posts where post_type = '.$this->postType;
        $sql .= ' limit ? , ?';

        return $sql;
    }

    private function getPostMetaSQL($postID, array $fieldsToRetrieve = null) {

        $sql = 'select meta_key, meta_value from wp_postmeta where ';

        $sql .= 'post_id = '.$postID;

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
                $dataRow->addDataItem(new DataItem('post.'.$key, $value));
            }

            $sql = $this->getPostMetaSQL($row['ID'], $fieldsToRetrieve);

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $postMetaFields = [];

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dataRow->addDataItem(new DataItem('post_meta.'.$row['meta_key'], $row['meta_value']));
            }

            $dataRows[] = $dataRow;
        }

        return $dataRows;

    }

    public function getFields() {
        return $this->fields;
    }
}