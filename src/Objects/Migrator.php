<?php

namespace DivineOmega\uxdm\Objects;

use DivineOmega\CliProgressBar\ProgressBar;
use DivineOmega\uxdm\Interfaces\DestinationInterface;
use DivineOmega\uxdm\Interfaces\SourceInterface;
use DivineOmega\uxdm\Objects\Exceptions\MissingFieldToMigrateException;
use DivineOmega\uxdm\Objects\Exceptions\NoDestinationException;
use DivineOmega\uxdm\Objects\Exceptions\NoSourceException;
use Psr\Cache\CacheItemPoolInterface;

class Migrator
{
    private $source;
    private $destinationContainers = [];
    private $fieldsToMigrate = [];
    private $keyFields = [];
    private $fieldMap = [];
    private $dataRowManipulator = null;
    private $dataItemManipulator = null;
    private $skipIfTrueCheck = null;
    private $sourceCachePool;
    private $sourceCacheKey;
    private $sourceCacheExpiresAfter;
    private $showProgressBar = false;
    private $progressBar;

    public function setSource(SourceInterface $source)
    {
        $this->source = $source;

        return $this;
    }

    public function setDestination(DestinationInterface $destination, array $fields = [])
    {
        $this->destinationContainers = [];
        $this->addDestination($destination, $fields);

        return $this;
    }

    public function addDestination(DestinationInterface $destination, array $fields = [])
    {
        $this->destinationContainers[] = new DestinationContainer($destination, $fields);

        return $this;
    }

    public function setFieldsToMigrate(array $fieldsToMigrate)
    {
        $this->fieldsToMigrate = $fieldsToMigrate;

        return $this;
    }

    public function setKeyFields(array $keyFields)
    {
        $this->keyFields = $keyFields;

        return $this;
    }

    public function setFieldMap(array $fieldMap)
    {
        $this->fieldMap = $fieldMap;

        return $this;
    }

    public function setDataItemManipulator(callable $dataItemManipulator)
    {
        $this->dataItemManipulator = $dataItemManipulator;

        return $this;
    }

    public function setDataRowManipulator(callable $dataRowManipulator)
    {
        $this->dataRowManipulator = $dataRowManipulator;

        return $this;
    }

    public function setSkipIfTrueCheck(callable $skipIfTrueCheck)
    {
        $this->skipIfTrueCheck = $skipIfTrueCheck;

        return $this;
    }

    public function setSourceCache(CacheItemPoolInterface $sourceCachePool, $sourceCacheKey, $sourceCacheExpiresAfter = 60 * 60 * 24)
    {
        $this->sourceCachePool = $sourceCachePool;
        $this->sourceCacheKey = $sourceCacheKey;
        $this->sourceCacheExpiresAfter = $sourceCacheExpiresAfter;

        return $this;
    }

    public function withProgressBar()
    {
        $this->showProgressBar = true;

        return $this;
    }

    private function getSourceDataRows($page)
    {
        if (!$this->sourceCachePool || !$this->sourceCacheKey) {
            return $this->source->getDataRows($page, $this->fieldsToMigrate);
        }

        $cacheItem = $this->sourceCachePool->getItem(sha1($this->sourceCacheKey.$page));

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $dataRows = $this->source->getDataRows($page, $this->fieldsToMigrate);
        $cacheItem->set($dataRows);
        $cacheItem->expiresAfter($this->sourceCacheExpiresAfter);
        $this->sourceCachePool->save($cacheItem);

        return $dataRows;
    }

    public function migrate()
    {
        if (!$this->source) {
            throw new NoSourceException('No source specified for migration.');
        }

        if (!$this->destinationContainers) {
            throw new NoDestinationException('No destination containers specified for migration.');
        }

        if (!$this->fieldsToMigrate) {
            $this->fieldsToMigrate = $this->source->getFields();
        }

        foreach (array_keys($this->fieldMap) as $sourceField) {
            if (!in_array($sourceField, $this->fieldsToMigrate)) {
                throw new MissingFieldToMigrateException('The source field `'.$sourceField.'` is present in the field map but not present in the fields to migrate.');
            }
        }

        foreach ($this->keyFields as $keyField) {
            if (!in_array($keyField, $this->fieldsToMigrate)) {
                throw new MissingFieldToMigrateException('The field `'.$keyField.'` is present in the key fields list but not present in the fields to migrate.');
            }
        }

        $nullDataItemManipulation = function () {
        };

        $results = [];

        if ($this->showProgressBar) {
            $this->progressBar = new ProgressBar();
            $this->progressBar->setMaxProgress($this->source->countPages() * count($this->destinationContainers));
            $this->progressBar->display();
        }

        for ($page = 1; $page < PHP_INT_MAX; $page++) {
            $dataRows = $this->getSourceDataRows($page);

            if (!$dataRows) {
                break;
            }

            foreach ($dataRows as $key => $dataRow) {
                $dataRow->prepare($this->keyFields, $this->fieldMap, $this->dataItemManipulator ? $this->dataItemManipulator : $nullDataItemManipulation);
            }

            if ($this->dataRowManipulator !== null) {
                $dataRowManipulator = $this->dataRowManipulator;
                foreach ($dataRows as $dataRow) {
                    $dataRowManipulator($dataRow);
                }
            }

            if ($this->skipIfTrueCheck !== null) {
                $skipIfTrueCheck = $this->skipIfTrueCheck;
                foreach ($dataRows as $key => $dataRow) {
                    if ($skipIfTrueCheck($dataRow)) {
                        unset($dataRows[$key]);
                    }
                }
            }

            foreach ($this->destinationContainers as $destinationContainer) {
                if (!$destinationContainer->fields) {
                    $results[] = $destinationContainer->destination->putDataRows($dataRows);
                    $this->advanceProgressBar();
                    continue;
                }

                $destinationDataRows = [];

                foreach ($dataRows as $dataRow) {
                    $destinationDataRow = new DataRow();
                    foreach ($dataRow->getDataItems() as $dataItem) {
                        if (in_array($dataItem->fieldName, $destinationContainer->fields)) {
                            $destinationDataRow->addDataItem($dataItem);
                        }
                    }
                    $destinationDataRows[] = $destinationDataRow;
                }

                $results[] = $destinationContainer->destination->putDataRows($destinationDataRows);
                $this->advanceProgressBar();
            }
        }

        foreach ($this->destinationContainers as $destinationContainer) {
            $destinationContainer->destination->finishMigration();
        }

        if ($this->showProgressBar) {
            $this->progressBar->complete();
        }

        return $results;
    }

    private function advanceProgressBar()
    {
        if ($this->showProgressBar) {
            $this->progressBar->advance()->display();
        }
    }
}
