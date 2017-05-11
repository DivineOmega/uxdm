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

    }
}