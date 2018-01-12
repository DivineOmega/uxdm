<?php

use PHPUnit\Framework\TestCase;
use RapidWeb\uxdm\Objects\Sources\JSONFilesSource;

final class JSONFilesSourceTest extends TestCase
{
    private function createSource()
    {
        $files = glob(__DIR__.'/Data/JSONFiles/*.json');

        return new JSONFilesSource($files);
    }

    public function testGetFields()
    {
        $source = $this->createSource();

        $this->assertEquals([], $source->getFields());
    }

    public function testGetDataRows()
    {
        $source = $this->createSource();

        $dataRows = $source->getDataRows(1, ['total', 'currency']);

        $this->assertCount(2, $dataRows);

        $dataItems = $dataRows[0]->getDataItems();

        $this->assertCount(2, $dataItems);

        $this->assertEquals('total', $dataItems[1]->fieldName);
        $this->assertEquals(25.98, $dataItems[1]->value);

        $this->assertEquals('currency', $dataItems[0]->fieldName);
        $this->assertEquals('GBP', $dataItems[0]->value);

        $dataItems = $dataRows[1]->getDataItems();

        $this->assertCount(2, $dataItems);

        $this->assertEquals('total', $dataItems[1]->fieldName);
        $this->assertEquals(51.96, $dataItems[1]->value);

        $this->assertEquals('currency', $dataItems[0]->fieldName);
        $this->assertEquals('GBP', $dataItems[0]->value);

        $dataRows = $source->getDataRows(2, ['name', 'weight']);

        $this->assertCount(0, $dataRows);
    }

    public function testGetDataRowsOnlyOneField()
    {
        $source = $this->createSource();

        $dataRows = $source->getDataRows(1, ['datePlaced']);

        $this->assertCount(2, $dataRows);

        $dataItems = $dataRows[0]->getDataItems();

        $this->assertCount(1, $dataItems);

        $this->assertEquals('datePlaced', $dataItems[0]->fieldName);
        $this->assertEquals(1443087352, $dataItems[0]->value);

        $dataItems = $dataRows[1]->getDataItems();

        $this->assertCount(1, $dataItems);

        $this->assertEquals('datePlaced', $dataItems[0]->fieldName);
        $this->assertEquals(1443087698, $dataItems[0]->value);

        $dataRows = $source->getDataRows(2, ['datePlaced']);

        $this->assertCount(0, $dataRows);
    }

    public function testAccessToArrays()
    {
        $source = $this->createSource();

        $dataRows = $source->getDataRows(1, ['items.0.quantity']);

        $this->assertCount(2, $dataRows);

        $dataItems = $dataRows[0]->getDataItems();

        $this->assertCount(1, $dataItems);

        $this->assertEquals('items.0.quantity', $dataItems[0]->fieldName);
        $this->assertEquals(2, $dataItems[0]->value);

        $dataItems = $dataRows[1]->getDataItems();

        $this->assertCount(1, $dataItems);

        $this->assertEquals('items.0.quantity', $dataItems[0]->fieldName);
        $this->assertEquals(4, $dataItems[0]->value);

        $dataRows = $source->getDataRows(2, ['name', 'weight']);

        $this->assertCount(0, $dataRows);
    }

}
