<?php

namespace DivineOmega\uxdm\Objects\Sources;

use DivineOmega\uxdm\Interfaces\SourceInterface;
use Exception;

class MSSQLSource extends PDOSource implements SourceInterface
{
    public function setOverrideSQL($overrideSQL) : self
    {
        $selectString = 'SELECT ';

        if (stripos($overrideSQL, $selectString) === false) {
            throw new Exception('PDO Source Override SQL must contain \''.$selectString.'\' to select source data.');
        }

        $this->overrideSQL = $overrideSQL;
        $this->fields = $this->getTableFields();

        return $this;
    }

    protected function getSQL($fieldsToRetrieve)
    {
        $fieldsSQL = implode(', ', $fieldsToRetrieve);

        $sql = 'select '.$fieldsSQL.' from '.$this->tableName;

        foreach ($this->joins as $join) {
            $sql .= $join->getSQL();
        }

        $sql .= ' ORDER BY '.$fieldsToRetrieve[0].' OFFSET ? ROWS , FETCH NEXT ? ROWS';

        if ($this->overrideSQL) {
            $sql = $this->overrideSQL;
        }

        return $sql;
    }
}
