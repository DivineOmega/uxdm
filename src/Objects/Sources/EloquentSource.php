<?php

namespace DivineOmega\uxdm\Objects\Sources;

use DivineOmega\uxdm\Interfaces\SourceInterface;
use DivineOmega\uxdm\Objects\DataItem;
use DivineOmega\uxdm\Objects\DataRow;

class EloquentSource implements SourceInterface
{
    private $model;
    private $queryCallback;
    private $fields = [];
    private $perPage = 10;

    public function __construct($eloquentModelClassName, $queryCallback = null)
    {
        $this->model = new $eloquentModelClassName();
        $this->queryCallback = $queryCallback;

        $this->fields = array_keys($this->model->first()->getAttributes());
    }

    public function getDataRows($page = 1, $fieldsToRetrieve = [])
    {
        $offset = ($page - 1) * $this->perPage;

        $query = $this->model->offset($offset)->limit($this->perPage);

        if (is_callable($this->queryCallback)) {
            $queryCallback = $this->queryCallback;
            $queryCallback($query);
        }

        $records = $query->get();

        $dataRows = [];

        foreach ($records as $record) {
            $attributes = array_dot($record->toArray());
            $dataRow = new DataRow();
            foreach ($fieldsToRetrieve as $key) {
                if (in_array($key, $fieldsToRetrieve) && array_key_exists($key, $attributes)) {
                    $dataRow->addDataItem(new DataItem($key, $attributes[$key]));
                }

            }
            $dataRows[] = $dataRow;
        }

        return $dataRows;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function countDataRows()
    {
        return $this->model->count();
    }

    public function countPages()
    {
        return ceil($this->countDataRows() / $this->perPage);
    }
}
