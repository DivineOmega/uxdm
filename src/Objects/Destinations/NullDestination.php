<?php

namespace RapidWeb\uxdm\Objects\Destinations;

use RapidWeb\uxdm\Interfaces\DestinationInterface;

class NullDestination implements DestinationInterface
{
    public function putDataRows(array $dataRows) {
        
    }
}