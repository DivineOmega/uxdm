<?php

namespace DivineOmega\uxdm\Interfaces;

interface DestinationInterface
{
    public function putDataRows(array $dataRows);
    public function finishMigration();
}
