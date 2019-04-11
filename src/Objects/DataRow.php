<?php

namespace DivineOmega\uxdm\Objects;

use DivineOmega\OmegaValidator\Validator;
use DivineOmega\uxdm\Objects\Exceptions\NoDataItemsInDataRowException;
use DivineOmega\uxdm\Objects\Exceptions\ValidationException;

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

    public function toArray()
    {
        $array = [];
        foreach ($this->dataItems as $dataItem) {
            $array[$dataItem->fieldName] = $dataItem->value;
        }

        return $array;
    }

    public function getDataItemByFieldName(string $fieldName)
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

    public function prepare(array $validationRules, array $keyFields, array $fieldMap, callable $dataItemManipulator)
    {
        $this->validate($validationRules);
        $this->setKeyFields($keyFields);
        $this->mapFields($fieldMap);

        if ($dataItemManipulator !== null) {
            $this->callDataItemManipulator($dataItemManipulator);
        }
    }

    private function validate(array $validationRules)
    {
        if (!$this->dataItems) {
            throw new NoDataItemsInDataRowException('Data row contains no data items. The specified source may be producing an invalid data row.');
        }

        if ($validationRules) {
            $validator = new Validator($this->toArray(), $validationRules);
            if ($validator->fails()) {
                $messages = print_r($validator->messages(), true);

                throw new ValidationException($messages);
            }
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
