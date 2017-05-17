<?php

namespace RapidWeb\uxdm\Objects;

use RapidWeb\uxdm\Interfaces\SourceInterface;
use RapidWeb\uxdm\Interfaces\DestinationInterface;
use RapidWeb\uxdm\Objects\Sources\BaseSource;
use Exception;

class Migrator
{
    private $source;
    private $destinationContainers = [];
    private $fieldsToMigrate = [];
    private $keyFields = [];
    private $fieldMap = [];
    private $dataRowManipulator;
    private $dataItemManipulator;
    private $skipIfTrueCheck;

    public function __construct() {
        $this->dataRowManipulator = function($dataRow) {};
        $this->dataItemManipulator = function($dataItem) {};
        $this->skipIfTrueCheck = function($dataRow) {};
    }

    public function setSource(SourceInterface $source) {
        $this->source = $source;
        return $this;
    }

    public function setDestination(DestinationInterface $destination, array $fields = []) {
        $this->destinationContainers = [];
        $this->addDestination($destination, $fields);
        return $this;
    }

    public function addDestination(DestinationInterface $destination, array $fields = []) {
        $this->destinationContainers[] = new DestinationContainer($destination, $fields);
        return $this;
    }

    public function setFieldsToMigrate(array $fieldsToMigrate) {
        $this->fieldsToMigrate = $fieldsToMigrate;
        return $this;
    }

    public function setKeyFields(array $keyFields) {
        $this->keyFields = $keyFields;
        return $this;
    }

    public function setFieldMap(array $fieldMap) {
        $this->fieldMap = $fieldMap;
        return $this;
    }

    public function setDataItemManipulator(callable $dataItemManipulator) {
        $this->dataItemManipulator = $dataItemManipulator;
        return $this;
    }

    public function setDataRowManipulator(callable $dataRowManipulator) {
        $this->dataRowManipulator = $dataRowManipulator;
        return $this;
    }

    public function setSkipIfTrueCheck(callable $skipIfTrueCheck) {
        $this->skipIfTrueCheck = $skipIfTrueCheck;
        return $this;
    }

    public function migrate() {

        if (!$this->source) {
            throw new Exception('No source specified for migration.');
        }

        if (!$this->destinationContainers) {
            throw new Exception('No destination containers specified for migration.');
        }

        if (!$this->fieldsToMigrate) {
            $this->fieldsToMigrate = $this->source->getFields();
        }

        $results = [];

        for ($page=1; $page < PHP_INT_MAX; $page++) { 

            $dataRows = $this->source->getDataRows($page, $this->fieldsToMigrate);

            if (!$dataRows) {
                break;
            }

            foreach($dataRows as $key => $dataRow) {
                $dataRow->prepare($this->keyFields, $this->fieldMap, $this->dataItemManipulator);
            }

            $dataRowManipulator = $this->dataRowManipulator;
            foreach($dataRows as $dataRow) {
                $dataRowManipulator($dataRow);
            }

            $skipIfTrueCheck = $this->skipIfTrueCheck;
            foreach($dataRows as $key => $dataRow) {
                if ($skipIfTrueCheck($dataRow)) {
                    unset($dataRows[$key]);
                }
            }

            foreach ($this->destinationContainers as $destinationContainer) {

                if (!$destinationContainer->fields) {
                    $results[] = $destinationContainer->destination->putDataRows($dataRows);
                    continue;
                }

                $dataRowsCopy = $dataRows;

                foreach($dataRowsCopy as $dataRow) {
                    foreach($dataRow->getDataItems() as $dataItem) {
                        if (!in_array($dataItem->fieldName, $destinationContainer->fields)) {
                            $dataRow->removeDataItem($dataItem);
                        }
                    }
                }

                $results[] = $destinationContainer->destination->putDataRows($dataRowsCopy);
            }
        }

        return $results;

    }
}