<?php

namespace RapidWeb\uxdm\Objects;

use RapidWeb\uxdm\Objects\DataItem;

class DataRow
{
    private $dataItems = [];

    public function addDataItem(DataItem $dataItem) {
        $this->dataItems[] = $dataItem;
    }

    public function getDataItems() {
        return $this->dataItems;
    }

    public function prepare(array $fieldsToMigrate, array $keyFields, array $fieldMap)
    {
        $this->removeUnnecessaryFields($fieldsToMigrate);
        $this->setKeyFields($keyFields);
        $this->mapFields($fieldMap);
    }

    private function removeUnnecessaryFields(array $fieldsToMigrate) {
        foreach($this->dataItems as $key => $dataItem) {
            if (!in_array($dataItem->fieldName, $fieldsToMigrate)) {
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