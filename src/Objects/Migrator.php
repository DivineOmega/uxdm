<?php

namespace RapidWeb\uxdm\Objects;

use RapidWeb\uxdm\Objects\Sources\BaseSource;
use RapidWeb\uxdm\Objects\Destinations\BaseDestination;

class Migrator
{
    private $source;
    private $destination;
    private $fieldsToMigrate = [];
    private $keyFields = [];
    private $fieldMap = [];

    public function setSource(Source $source) {
        $this->source = $source;
        return $this;
    }

    public function setDestination(Destination $destination) {
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

    public function migrate() {

        $results = [];

        for ($page=1; $page < PHP_INT_MAX; $page++) { 

            $dataRows = $this->source->getDataRows($page);

            if (!$dataRows) {
                break;
            }

            foreach($dataRows as $key => $dataRow) {
                $dataRow->prepare($this->fieldsToMigrate, $this->keyFields, $this->fieldMap);
                $dataRow[$key] = $dataRow;
            }

            $results[] = $this->destination->putDataRows($dataRows);
        }

        return $results;

    }
}