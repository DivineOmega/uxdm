<?php

namespace DivineOmega\uxdm\Objects\Sources;

use DivineOmega\uxdm\Interfaces\SourceInterface;
use DivineOmega\uxdm\Objects\DataItem;
use DivineOmega\uxdm\Objects\DataRow;

class NDJSONSource implements SourceInterface
{
    protected $file;
    protected $fields = [];
    protected $perPage = 10;

    public function __construct($file)
    {
        $this->file = $file;
    }

    protected function iterateFile()
    {
        $file = new \SplFileObject($this->file, 'r');
        while (!$file->eof()) {
            $line = trim($file->fgets());
            if ($line === '') {
                continue;
            }
            $data = json_decode($line, true);
            if ($data === null && strtolower($line) !== 'null') {
                continue;
            }
            yield $data;
        }
    }

    public function getDataRows(int $page = 1, array $fieldsToRetrieve = []): array
    {
        $offset = 0 + (($page - 1) * $this->perPage);

        $dataRows = [];
        $index = 0;
        foreach ($this->iterateFile() as $record) {
            if ($index >= $offset && $index < $offset + $this->perPage) {
                $dotted = array_dot($record);
                $dataRow = new DataRow();
                foreach ($dotted as $key => $value) {
                    if (in_array($key, $fieldsToRetrieve)) {
                        $dataRow->addDataItem(new DataItem($key, $value));
                    }
                }
                $dataRows[] = $dataRow;
            }
            if ($index >= $offset + $this->perPage - 1) {
                break;
            }
            $index++;
        }

        return $dataRows;
    }

    public function getFields(): array
    {
        if (!$this->fields) {
            foreach ($this->iterateFile() as $record) {
                $this->fields = array_merge($this->fields, array_keys(array_dot($record)));
            }
            $this->fields = array_unique($this->fields);
        }

        return $this->fields;
    }

    public function countDataRows(): int
    {
        $file = new \SplFileObject($this->file, 'r');
        $file->seek(PHP_INT_MAX);

        return $file->key() + 1;
    }

    public function countPages(): int
    {
        return ceil($this->countDataRows() / $this->perPage);
    }
}
