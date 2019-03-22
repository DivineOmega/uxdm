<?php

namespace DivineOmega\uxdm\Objects\Destinations;

use DivineOmega\uxdm\Interfaces\DestinationInterface;

class DebugOutputDestination implements DestinationInterface
{
    public function putDataRows(array $dataRows): void
    {
        var_dump($dataRows);
    }

    public function finishMigration(): void
    {
    }
}
