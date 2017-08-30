<?php

namespace RapidWeb\uxdm\Objects;

use RapidWeb\uxdm\Interfaces\SourceInterface;
use RapidWeb\uxdm\Interfaces\DestinationInterface;
use RapidWeb\uxdm\Objects\Sources\BaseSource;
use RapidWeb\uxdm\Objects\DataRow;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use RapidWeb\uxdm\Objects\Exceptions\NoSourceException;
use RapidWeb\uxdm\Objects\Exceptions\NoDestinationException;
use RapidWeb\uxdm\Objects\Exceptions\MissingFieldToMigrateException;

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
    private $sourceCachePool;
    private $sourceCacheKey;
    private $sourceCacheExpiresAfter;

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

    public function setSourceCache(CacheItemPoolInterface $sourceCachePool, $sourceCacheKey, $sourceCacheExpiresAfter = 60*60*24) {
        $this->sourceCachePool = $sourceCachePool;
        $this->sourceCacheKey = $sourceCacheKey;
        $this->sourceCacheExpiresAfter = $sourceCacheExpiresAfter;
        return $this;
    }

    private function getSourceDataRows($page) {

        if (!$this->sourceCachePool || !$this->sourceCacheKey) {
            return $this->source->getDataRows($page, $this->fieldsToMigrate);
        }

        $cacheItem = $this->sourceCachePool->getItem(sha1($this->sourceCacheKey.$page));
        $dataRows = $cacheItem->get();

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $dataRows = $this->source->getDataRows($page, $this->fieldsToMigrate);
        $cacheItem->set($dataRows);
        $cacheItem->expiresAfter($this->sourceCacheExpiresAfter);
        $this->sourceCachePool->save($cacheItem);

        return $dataRows;
    }

    public function migrate() {

        if (!$this->source) {
            throw new NoSourceException('No source specified for migration.');
        }

        if (!$this->destinationContainers) {
            throw new NoDestinationException('No destination containers specified for migration.');
        }

        if (!$this->fieldsToMigrate) {
            $this->fieldsToMigrate = $this->source->getFields();
        }

        foreach(array_keys($this->fieldMap) as $sourceField) {
            if (!in_array($sourceField, $this->fieldsToMigrate)) {
                throw new MissingFieldToMigrateException('The source field `'.$sourceField.'` is present in the field map but not present in the fields to migrate.');
            }
        }

        foreach($this->keyFields as $keyField) {
            if (!in_array($keyField, $this->fieldsToMigrate)) {
                throw new MissingFieldToMigrateException('The field `'.$keyField.'` is present in the key fields list but not present in the fields to migrate.');
            }
        }

        $results = [];

        for ($page=1; $page < PHP_INT_MAX; $page++) { 

            $dataRows = $this->getSourceDataRows($page);
            
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

                $destinationDataRows = [];

                foreach($dataRows as $dataRow) {
                    $destinationDataRow = new DataRow();
                    foreach($dataRow->getDataItems() as $dataItem) {
                        if (in_array($dataItem->fieldName, $destinationContainer->fields)) {
                            $destinationDataRow->addDataItem($dataItem);
                        }
                    }
                    $destinationDataRows[] = $destinationDataRow;
                }

                $results[] = $destinationContainer->destination->putDataRows($destinationDataRows);
            }
        }

        return $results;

    }
}