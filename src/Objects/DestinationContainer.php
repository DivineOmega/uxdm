<?php

namespace DivineOmega\uxdm\Objects;

use DivineOmega\uxdm\Interfaces\DestinationInterface;

class DestinationContainer
{
    public $destination;
    public $fields = [];

    public function __construct(DestinationInterface $destination, array $fields)
    {
        $this->destination = $destination;
        $this->fields = $fields;
    }
}
