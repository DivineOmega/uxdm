<?php

namespace RapidWeb\uxdm\Objects;

use RapidWeb\uxdm\Interfaces\SourceInterface;
use RapidWeb\uxdm\Interfaces\DestinationInterface;
use RapidWeb\uxdm\Objects\Sources\BaseSource;
use Exception;

class Migrator
{
    private $source;
    private $destination;
    private $fieldsToMigrate = [];
    private $keyFields = [];
    private $fieldMap = [];
    private $dataItemManipulator;
    private $skipIfTrueCheck;

    public function __construct() {
        $this->dataItemManipulator = function($dataItem) {};
        $this->skipIfTrueCheck = function($dataRow) {};
    }

    public function setSource(SourceInterface $source) {
        $this->source = $source;
        return $this;
    }

    public function setDestination(DestinationInterface $destination) {
        $this->destination = $destination;
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

    public function setSkipIfTrueCheck(callable $skipIfTrueCheck) {
        $this->skipIfTrueCheck = $skipIfTrueCheck;
        return $this;
    }

    public function migrate() {

        if (!$this->source) {
            throw new Exception('No source specified for migration.');
        }

        if (!$this->destination) {
            throw new Exception('No destination specified for migration.');
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
                $dataRows[$key] = $dataRow;
            }

            $skipIfTrueCheck = $this->skipIfTrueCheck;
            foreach($dataRows as $key => $dataRow) {
                if ($skipIfTrueCheck && $skipIfTrueCheck($dataRow)) {
                    unset($dataRows[$key]);
                }
            }

            $results[] = $this->destination->putDataRows($dataRows);
        }

        return $results;

    }
}