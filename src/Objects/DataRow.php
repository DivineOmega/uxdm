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

    public function getKeyDataItems() {

        $keyDataItems = [];

        foreach($this->dataItems as $dataItem) {
            if ($dataItem->keyField) {
                $keyDataItems[] = $dataItem;
            }
        }

        return $keyDataItems;
    }

    public function prepare(array $keyFields, array $fieldMap, callable $dataItemManipulator)
    {
        $this->setKeyFields($keyFields);
        $this->mapFields($fieldMap);
        $this->callDataItemManipulator($dataItemManipulator);
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

    private function callDataItemManipulator(callable $dataItemManipulator) {
        if (!$dataItemManipulator) {
            return;
        }
        foreach($this->dataItems as $key => $dataItem) {
            $dataItemManipulator($dataItem);
        }
    }
}