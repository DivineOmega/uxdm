<?php

namespace DivineOmega\uxdm\Interfaces;

use DivineOmega\uxdm\Objects\DataRow;

interface TransformerInterface
{
    public function transform(DataRow &$dataRow): void;
}