<?php

namespace RapidWeb\uxdm\Objects\Sources;

use RapidWeb\uxdm\Interfaces\SourceInterface;
use RapidWeb\uxdm\Objects\DataRow;
use RapidWeb\uxdm\Objects\DataItem;

class CSVSource implements SourceInterface
{
    private $file;
    private $fields = [];

    public function __construct($file) {
        $this->file = $file;
        $this->fields = reset($this->getCSVLines(0, 1));
    }

    private function getCSVLines($offset, $amount) {

        $lines = [];
        $lineCount = 0;
        $fh = fopen($this->file, 'r');

        while (($line = fgetcsv($fh)) !== false) {

            if ($lineCount >= $offset && $lineCount < $offset + $amount) {
                $lines[] = $line;
            }
            
            if($lineCount >= $offset + $amount) {
                break;
            }

            $lineCount++;
        }
        
        return $lines;
    }    

    public function getDataRows($page = 1) {

        $perPage = 10;

        $offset = 1 + (($page-1) * $perPage);
        
        $lines = $this->getCSVLines($offset, $perPage);

        $dataRows = [];

        foreach($lines as $line) {
            $dataRow = new DataRow;
            
            foreach($line as $key => $value) {
                $dataItem = new DataItem;
                $dataItem->fieldName = $this->fields[$key];
                $dataItem->value = $value;
                $dataRow->addDataItem($dataItem);
            }

            $dataRows[] = $dataRow;
        }

        return $dataRows;

    }

    public function getFields() {
        return $this->fields;
    }
}