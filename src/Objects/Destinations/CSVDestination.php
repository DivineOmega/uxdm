<?php

namespace RapidWeb\uxdm\Objects\Destinations;

use RapidWeb\uxdm\Interfaces\DestinationInterface;

class CSVDestination implements DestinationInterface
{
    private $file;
    private $rowNum = 0;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function putDataRows(array $dataRows)
    {
        if ($this->rowNum === 0) {
            $fh = fopen($this->file, 'w');
        } else {
            $fh = fopen($this->file, 'a');
        }

        foreach ($dataRows as $dataRow) {
            $dataItems = $dataRow->getDataItems();

            if ($this->rowNum === 0) {
                $fieldNames = [];
                foreach ($dataItems as $dataItem) {
                    $fieldNames[] = $dataItem->fieldName;
                }
                fputcsv($fh, $fieldNames);
            }

            $values = [];
            foreach ($dataItems as $dataItem) {
                $values[] = $dataItem->value;
            }
            fputcsv($fh, $values);

            $this->rowNum++;
        }
    }
}
