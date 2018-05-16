<?php

namespace DivineOmega\uxdm\Objects\Destinations;

use DivineOmega\uxdm\Interfaces\DestinationInterface;
use DivineOmega\uxdm\Objects\DataRow;

class EloquentDestination implements DestinationInterface
{
    private $model;

    public function __construct($eloquentModelClassName)
    {
        $this->model = new $eloquentModelClassName();
    }

    private function alreadyExists(array $keyDataItems)
    {
        $count = $this->model->where(function ($query) use ($keyDataItems) {
            foreach ($keyDataItems as $keyDataItem) {
                $query->where($keyDataItem->fieldName, $keyDataItem->value);
            }
        })->count();

        return $count > 0;
    }

    private function insertDataRow(DataRow $dataRow)
    {
        $dataItems = $dataRow->getDataItems();

        $newRecord = (new \ReflectionObject($this->model))->newInstance();

        foreach ($dataItems as $dataItem) {
            $newRecord->setAttribute($dataItem->fieldName, $dataItem->value);
        }

        $newRecord->save();
    }

    private function updateDataRow(DataRow $dataRow)
    {
        $dataItems = $dataRow->getDataItems();
        $keyDataItems = $dataRow->getKeyDataItems();

        $record = $this->model->where(function ($query) use ($keyDataItems) {
            foreach ($keyDataItems as $keyDataItem) {
                $query->where($keyDataItem->fieldName, $keyDataItem->value);
            }
        })->first();

        foreach ($dataItems as $dataItem) {
            $record->setAttribute($dataItem->fieldName, $dataItem->value);
        }

        $record->save();
    }

    public function putDataRows(array $dataRows)
    {
        foreach ($dataRows as $dataRow) {
            $keyDataItems = $dataRow->getKeyDataItems();

            if (!$keyDataItems) {
                $this->insertDataRow($dataRow);
                continue;
            }

            if ($this->alreadyExists($keyDataItems)) {
                $this->updateDataRow($dataRow);
            } else {
                $this->insertDataRow($dataRow);
            }
        }
    }
}
