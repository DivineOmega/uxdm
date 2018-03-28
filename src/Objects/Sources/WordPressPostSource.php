<?php

namespace RapidWeb\uxdm\Objects\Sources;

use PDO;
use PDOStatement;
use RapidWeb\uxdm\Interfaces\SourceInterface;
use RapidWeb\uxdm\Objects\DataItem;
use RapidWeb\uxdm\Objects\DataRow;

class WordPressPostSource implements SourceInterface
{
    private $pdo;
    private $fields = [];
    private $postType;

    public function __construct(PDO $pdo, $postType = 'post')
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
        $this->postType = $postType;
        $this->fields = $this->getPostFields();
    }

    private function getPostFields()
    {
        $sql = $this->getPostSQL(['*']);

        $stmt = $this->pdo->prepare($sql);
        $this->bindLimitParameters($stmt, 0, 1);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $postFields = array_keys($row);

        foreach ($postFields as $key => $postField) {
            $postFields[$key] = 'wp_posts.'.$postField;
        }

        $sql = $this->getPostMetaSQL($row['ID']);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $postMetaFields = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $postMetaFields[] = 'wp_postmeta.'.$row['meta_key'];
        }

        return array_merge($postFields, $postMetaFields);
    }

    private function getPostSQL($fieldsToRetrieve)
    {
        foreach ($fieldsToRetrieve as $key => $fieldToRetrieve) {
            if (strpos($fieldToRetrieve, 'wp_posts.') !== 0 && $fieldToRetrieve !== '*') {
                unset($fieldsToRetrieve[$key]);
            }
        }

        $fieldsSQL = implode(', ', $fieldsToRetrieve);

        $sql = 'select '.$fieldsSQL.' from wp_posts where post_type = \''.$this->postType.'\'';
        $sql .= ' limit ? , ?';

        return $sql;
    }

    private function getPostMetaSQL($postID, array $fieldsToRetrieve = null)
    {
        $sql = 'select meta_key, meta_value from wp_postmeta where ';

        $sql .= 'post_id = '.$postID;

        if ($fieldsToRetrieve) {
            foreach ($fieldsToRetrieve as $key => $fieldToRetrieve) {
                if (strpos($fieldToRetrieve, 'wp_postmeta.') !== 0) {
                    unset($fieldsToRetrieve[$key]);
                }
                $fieldsToRetrieve[$key] = str_replace('wp_postmeta.', '', $fieldToRetrieve);
            }

            $sql .= ' and ( ';
            foreach ($fieldsToRetrieve as $fieldToRetrieve) {
                $sql .= ' meta_key = \''.$fieldToRetrieve.'\' or ';
            }
            $sql = substr($sql, 0, -3);
            $sql .= ' ) ';
        }

        return $sql;
    }

    private function bindLimitParameters(PDOStatement $stmt, $offset, $perPage)
    {
        $stmt->bindValue(1, $offset, PDO::PARAM_INT);
        $stmt->bindValue(2, $perPage, PDO::PARAM_INT);
    }

    public function getDataRows($page = 1, $fieldsToRetrieve = [])
    {
        $perPage = 10;

        $offset = (($page - 1) * $perPage);

        $postsSql = $this->getPostSQL($fieldsToRetrieve);

        $postsStmt = $this->pdo->prepare($postsSql);
        $this->bindLimitParameters($postsStmt, $offset, $perPage);

        $postsStmt->execute();

        $dataRows = [];

        while ($postsRow = $postsStmt->fetch(PDO::FETCH_ASSOC)) {
            $dataRow = new DataRow();

            foreach ($postsRow as $key => $value) {
                $dataRow->addDataItem(new DataItem('wp_posts.'.$key, $value));
            }

            if (isset($postsRow['ID'])) {
                $postMetaSql = $this->getPostMetaSQL($postsRow['ID'], $fieldsToRetrieve);

                $postMetaStmt = $this->pdo->prepare($postMetaSql);
                $postMetaStmt->execute();

                while ($postMetaRow = $postMetaStmt->fetch(PDO::FETCH_ASSOC)) {
                    $dataRow->addDataItem(new DataItem('wp_postmeta.'.$postMetaRow['meta_key'], $postMetaRow['meta_value']));
                }
            }

            $dataRows[] = $dataRow;
        }

        return $dataRows;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function countDataRows()
    {
        $sql = $this->getPostSQL([]);
        $fromPos = stripos($sql, 'from');
        $limitPos = strripos($sql, 'limit');
        $sqlSuffix = substr($sql, $fromPos, $limitPos - $fromPos);

        $sql = 'count (*) '.$sqlSuffix;
    }
}
