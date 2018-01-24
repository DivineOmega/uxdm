<?php

namespace RapidWeb\uxdm\Objects\Sources;

use RapidWeb\uxdm\Interfaces\SourceInterface;
use RapidWeb\uxdm\Objects\DataItem;
use RapidWeb\uxdm\Objects\DataRow;

class AssociativeArraySource implements SourceInterface
{
    private $array;
    private $fields = [];

    public function __construct(array &$array)
    {
        $this->array = &$array;

        if (isset($array[0]) && is_array($array[0])) {
            $this->fields = array_keys($array[0]);
        }
    }

    public function getDataRows($page = 1, $fieldsToRetrieve = [])
    {
        $perPage = 10;

        $offset = 0 + (($page - 1) * $perPage);

        $arrayRows = array_slice($this->array, $offset, $perPage);

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
}
