<?php

namespace DivineOmega\uxdm\TestIntegrationClasses;

use DivineOmega\uxdm\Interfaces\TransformerInterface;
use DivineOmega\uxdm\Objects\DataItem;
use DivineOmega\uxdm\Objects\DataRow;

class Md5NameTransformer implements TransformerInterface
{
    public function transform(DataRow &$dataRow): void
    {
        $dataRow->addDataItem(new DataItem('md5_name', md5($dataRow->getDataItemByFieldName('name')->value)));
    }
}