<?php

namespace DivineOmega\uxdm\Objects\Sources;

use DivineOmega\uxdm\Interfaces\SourceInterface;
use DivineOmega\uxdm\Objects\DataItem;
use DivineOmega\uxdm\Objects\DataRow;

class AssociativeArraySource implements SourceInterface
{
    private $array;
    private $fields = [];
    private $perPage = 10;

    public function __construct(array &$array)
    {
        $this->array = &$array;

        if (isset($array[0]) && is_array($array[0])) {
            $this->fields = array_keys($array[0]);
        }
    }

    public function getDataRows($page = 1, $fieldsToRetrieve = [])
    {
        $offset = 0 + (($page - 1) * $this->perPage);

        $arrayRows = array_slice($this->array, $offset, $this->perPage);

        $dataRows = [];

        foreach ($arrayRows as $arrayRow) {
            $dataRow = new DataRow();

            foreach ($arrayRow as $key => $value) {
                if (in_array($key, $fieldsToRetrieve)) {
                    $dataRow->addDataItem(new DataItem($key, $value));
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
        return count($this->array);
    }

    public function countPages()
    {
        return ceil($this->countDataRows() / $this->perPage);
    }
}
