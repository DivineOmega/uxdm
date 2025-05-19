<?php

use DivineOmega\uxdm\Objects\Sources\NDJSONSource;
use PHPUnit\Framework\TestCase;

final class NDJSONSourceTest extends TestCase
{
    private function createSource()
    {
        return new NDJSONSource(__DIR__.'/Data/source.ndjson');
    }

    public function testGetFields()
    {
        $source = $this->createSource();

        $this->assertEquals(['name', 'numbers.0', 'numbers.1'], $source->getFields());
    }

    public function testGetDataRows()
    {
        $source = $this->createSource();

        $dataRows = $source->getDataRows(1, ['name', 'numbers.0']);

        $this->assertCount(2, $dataRows);

        $dataItems = $dataRows[0]->getDataItems();
        $this->assertCount(2, $dataItems);
        $this->assertEquals('name', $dataItems[0]->fieldName);
        $this->assertEquals('John', $dataItems[0]->value);
        $this->assertEquals('numbers.0', $dataItems[1]->fieldName);
        $this->assertEquals(1, $dataItems[1]->value);

        $dataItems = $dataRows[1]->getDataItems();
        $this->assertCount(2, $dataItems);
        $this->assertEquals('name', $dataItems[0]->fieldName);
        $this->assertEquals('Jane', $dataItems[0]->value);
        $this->assertEquals('numbers.0', $dataItems[1]->fieldName);
        $this->assertEquals(3, $dataItems[1]->value);

        $dataRows = $source->getDataRows(2, ['name']);
        $this->assertCount(0, $dataRows);
    }

    public function testCountDataRows()
    {
        $source = $this->createSource();

        $this->assertEquals(2, $source->countDataRows());
    }

    public function testCountPages()
    {
        $source = $this->createSource();

        $this->assertEquals(1, $source->countPages());
    }
}
