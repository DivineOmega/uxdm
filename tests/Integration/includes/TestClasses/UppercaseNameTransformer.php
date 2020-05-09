<?php

namespace DivineOmega\uxdm\TestIntegrationClasses;

use DivineOmega\uxdm\Interfaces\TransformerInterface;
use DivineOmega\uxdm\Objects\DataRow;

class UppercaseNameTransformer implements TransformerInterface
{
    public function transform(DataRow &$dataRow): void
    {
        $dataItem = $dataRow->getDataItemByFieldName('name');

        if ($dataItem) {
            $dataItem->value = strtoupper($dataItem->value);
        }
    }
}