<?php

namespace RapidWeb\uxdm\Objects;

use RapidWeb\uxdm\Interfaces\DestinationInterface;

class DestinationContainer
{
    public $destination;
    public $fields = [];

    public function __construct(DestinationInterface $destination, array $fields) {
        $this->destination = $destination;
        $this->fields = $fields;
    }

}