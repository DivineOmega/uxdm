<?php

namespace RapidWeb\uxdm\Objects;

use RapidWeb\uxdm\Objects\Exceptions\NoDataItemsInDataRowException;

class DataRow
{
    private $dataItems = [];

    public function addDataItem(DataItem $dataItem)
    {
        $this->dataItems[] = $dataItem;
    }

    public function removeDataItem(DataItem $dataItemToDelete)
    {
        foreach ($this->dataItems as $key => $dataItem) {
            if ($dataItem == $dataItemToDelete) {
                unset($this->dataItems[$key]);
                break;
            }
        }
    }

    public function getDataItems()
    {
        return $this->dataItems;
    }

    public function getDataItemByFieldName($fieldName)
    {
        foreach ($this->dataItems as $dataItem) {
            if ($dataItem->fieldName == $fieldName) {
                return $dataItem;
            }
        }
    }

    public function getKeyDataItems()
    {
        $keyDataItems = [];

        foreach ($this->dataItems as $dataItem) {
            if ($dataItem->keyField) {
                $keyDataItems[] = $dataItem;
            }
        }

        return $keyDataItems;
    }

    public function prepare(array $keyFields, array $fieldMap, callable $dataItemManipulator)
    {
        $this->validate();
        $this->setKeyFields($keyFields);
        $this->mapFields($fieldMap);

        if ($dataItemManipulator !== null) {
            $this->callDataItemManipulator($dataItemManipulator);
        }
    }

    private function validate()
    {
        if (!$this->dataItems) {
            throw new NoDataItemsInDataRowException('Data row contains no data items. The specified source may be producing an invalid data row.');
        }
    }

    private function setKeyFields(array $keyFields)
    {
        foreach ($this->dataItems as $key => $dataItem) {
            if (in_array($dataItem->fieldName, $keyFields)) {
                $dataItem->keyField = true;
                $dataItems[$key] = $dataItem;
            }
        }
    }

    private function mapFields(array $fieldMap)
    {
        foreach ($this->dataItems as $key => $dataItem) {
            if (array_key_exists($dataItem->fieldName, $fieldMap)) {
                $newFieldName = $fieldMap[$dataItem->fieldName];
                $dataItem->fieldName = $newFieldName;
                $dataItems[$key] = $dataItem;
            }
        }
    }

    private function callDataItemManipulator(callable $dataItemManipulator)
    {
        foreach ($this->dataItems as $key => $dataItem) {
            $dataItemManipulator($dataItem);
        }
    }
}
