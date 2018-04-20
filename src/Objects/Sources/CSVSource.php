<?php

namespace DivineOmega\uxdm\Objects\Sources;

use DivineOmega\uxdm\Interfaces\SourceInterface;
use DivineOmega\uxdm\Objects\DataItem;
use DivineOmega\uxdm\Objects\DataRow;

class CSVSource implements SourceInterface
{
    private $file;
    private $fields = [];
    private $perPage = 10;

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
        $offset = 1 + (($page - 1) * $this->perPage);

        $lines = $this->getCSVLines($offset, $this->perPage);

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

    public function countPages()
    {
        return ceil($this->countDataRows() / $this->perPage);
    }
}
