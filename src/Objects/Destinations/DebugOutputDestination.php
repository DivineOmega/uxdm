<?php

namespace RapidWeb\uxdm\Objects\Destinations;

use RapidWeb\uxdm\Interfaces\DestinationInterface;

class DebugOutputDestination implements DestinationInterface
{
    public function putDataRows(array $dataRows)
    {
        var_dump($dataRows);
    }
}
