<?php

namespace RapidWeb\uxdm\Objects;

use RapidWeb\uxdm\Objects\DataItem;

class DataRow
{
    private $dataItems = [];

    public function prepare(array $fieldsToMigrate, array $keyFields, array $fieldMap)
    {
        $this->removeUnnecessaryFields($this->fieldsToMigrate);
        $this->setKeyFields($this->keyFields);
        $this->mapFields($this->fieldMap);
    }

    private function removeUnnecessaryFields(array $fieldsToMigrate) {
        foreach($this->dataItems as $key => $dataItem) {
            if (!in_array($dataItem->fieldName, $fields)) {
                unset($this->dataItems[$key]);
            }
        }
    }

    private function setKeyFields(array $keyFields) {
        foreach($this->dataItems as $key => $dataItem) {
            if (in_array($dataItem->fieldName, $keyFields)) {
                $dataItem->keyField = true;
                $dataItems[$key] = $dataItem;
            }
        }
    }

    private function mapFields(array $fieldMap) {
        foreach($this->dataItems as $key => $dataItem) {
            if (array_key_exists($dataItem->fieldName, $fieldMap)) {
                $newFieldName = $fieldMap[$dataItem->fieldName];
                $dataItem->fieldName = $newFieldName;
                $dataItems[$key] = $dataItem;
            }
        }
    }
}