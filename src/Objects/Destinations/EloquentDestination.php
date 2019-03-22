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

    private function getAssocArrayFromDataRow(DataRow $dataRow)
    {
        $dataArray = [];
        $dataItems = $dataRow->getDataItems();

        foreach ($dataItems as $dataItem) {
            $dataArray[$dataItem->fieldName] = $dataItem->value;
        }

        return $dataArray;
    }

    private function insertDataRow(DataRow $dataRow)
    {
        $this->model->create($this->getAssocArrayFromDataRow($dataRow));
    }

    private function updateDataRow(DataRow $dataRow)
    {
        $keyDataItems = $dataRow->getKeyDataItems();

        $this->model->where(function ($query) use ($keyDataItems) {
            foreach ($keyDataItems as $keyDataItem) {
                $query->where($keyDataItem->fieldName, $keyDataItem->value);
            }
        })->update($this->getAssocArrayFromDataRow($dataRow));
    }

    public function putDataRows(array $dataRows): void
    {
        $this->model->unguard();

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

        $this->model->reguard();
    }

    public function finishMigration(): void
    {
    }
}
