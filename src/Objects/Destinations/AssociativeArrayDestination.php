<?php

namespace DivineOmega\uxdm\Objects\Destinations;

use DivineOmega\uxdm\Interfaces\DestinationInterface;

class AssociativeArrayDestination implements DestinationInterface
{
    private $array;

    public function __construct(array &$array)
    {
        $this->array = &$array;
    }

    public function putDataRows(array $dataRows)
    {
        foreach ($dataRows as $dataRow) {
            $dataItems = $dataRow->getDataItems();

            $row = [];

            foreach ($dataItems as $dataItem) {
                $row[$dataItem->fieldName] = $dataItem->value;
            }

            $this->array[] = $row;
        }
    }

    public function finishMigration()
    {
        
    }
}
