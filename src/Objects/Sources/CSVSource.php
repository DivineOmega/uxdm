<?php

namespace RapidWeb\uxdm\Objects\Sources;

use RapidWeb\uxdm\Interfaces\SourceInterface;
use RapidWeb\uxdm\Objects\DataItem;
use RapidWeb\uxdm\Objects\DataRow;

class CSVSource implements SourceInterface
{
    private $file;
    private $fields = [];

    public function __construct($file)
    {
        $this->file = $file;

        $firstCSVLine = $this->getCSVLines(0, 1);
        $this->fields = reset($firstCSVLine);
    }

    private function getCSVLines($offset, $amount)
    {
        $lines = [];
        $lineCount = 0;
        $fh = fopen($this->file, 'r');

        while (($line = fgetcsv($fh)) !== false) {
            if ($lineCount >= $offset && $lineCount < $offset + $amount) {
                $lines[] = $line;
            }

            if ($lineCount >= $offset + $amount) {
                break;
            }

            $lineCount++;
        }

        return $lines;
    }

    public function getDataRows($page = 1, $fieldsToRetrieve = [])
    {
        $perPage = 10;

        $offset = 1 + (($page - 1) * $perPage);

        $lines = $this->getCSVLines($offset, $perPage);

        $dataRows = [];

        foreach ($lines as $line) {
            $dataRow = new DataRow();

            foreach ($line as $key => $value) {
                if (in_array($this->fields[$key], $fieldsToRetrieve)) {
                    $dataRow->addDataItem(new DataItem($this->fields[$key], $value));
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
        $file = new \SplFileObject($this->file, 'r');
        $file->seek(PHP_INT_MAX);
        return $file->key();
    }
}
