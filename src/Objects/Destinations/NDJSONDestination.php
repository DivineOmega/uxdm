<?php

namespace DivineOmega\uxdm\Objects\Destinations;

use DivineOmega\uxdm\Interfaces\DestinationInterface;

class NDJSONDestination implements DestinationInterface
{
    protected $file;
    protected $rowNum = 0;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function putDataRows(array $dataRows): void
    {
        if ($this->rowNum === 0) {
            $fh = fopen($this->file, 'w');
        } else {
            $fh = fopen($this->file, 'a');
        }

        foreach ($dataRows as $dataRow) {
            $dataItems = $dataRow->getDataItems();
            $row = [];
            foreach ($dataItems as $dataItem) {
                $row[$dataItem->fieldName] = $dataItem->value;
            }
            $array = array_undot($row);
            fwrite($fh, json_encode($array).PHP_EOL);
            $this->rowNum++;
        }

        fclose($fh);
    }

    public function finishMigration(): void
    {
    }
}
