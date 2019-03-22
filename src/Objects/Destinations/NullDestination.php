<?php

namespace DivineOmega\uxdm\Objects\Destinations;

use DivineOmega\uxdm\Interfaces\DestinationInterface;

class NullDestination implements DestinationInterface
{
    public function putDataRows(array $dataRows): void
    {
    }

    public function finishMigration(): void
    {
    }
}
