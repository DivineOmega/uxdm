<?php

use PHPUnit\Framework\TestCase;
use Faker\Factory;

use RapidWeb\uxdm\Objects\DataRow;
use RapidWeb\uxdm\Objects\DataItem;

final class DataRowTest extends TestCase
{
    private function createDataItem()
    {
        $faker = Faker\Factory::create();

        $fieldName = $faker->word;
        $value = $faker->word;

        $dataItem = new DataItem($fieldName, $value);

        return $dataItem;
    }

    public function testDataRowCreation()
    {
        $dataRow = new DataRow();

        $this->assertCount(0, $dataRow->getDataItems());
        $this->assertCount(0, $dataRow->getKeyDataItems());
    }

    public function testDataItemAddition()
    {
        $dataItem = $this->createDataItem();

        $dataRow = new DataRow();
        $dataRow->addDataItem($dataItem);

        $this->assertCount(1, $dataRow->getDataItems());
        $this->assertCount(0, $dataRow->getKeyDataItems());

        $dataItems = $dataRow->getDataItems();

        $this->assertEquals($dataItems[0], $dataItem);
    }

    public function testKeyDataItemAddition()
    {
        $dataItem = $this->createDataItem();
        $dataItem->keyField = true;

        $dataRow = new DataRow();
        $dataRow->addDataItem($dataItem);

        $this->assertCount(1, $dataRow->getDataItems());
        $this->assertCount(1, $dataRow->getKeyDataItems());

        $dataItems = $dataRow->getDataItems();

        $this->assertEquals($dataItems[0], $dataItem);
    }

    public function testDataItemRemoval()
    {
        $dataItem = $this->createDataItem();

        $dataRow = new DataRow();
        $dataRow->addDataItem($dataItem);

        $this->assertCount(1, $dataRow->getDataItems());

        $dataRow->removeDataItem($dataItem);

        $this->assertCount(0, $dataRow->getDataItems());
    }

    public function testGetDataItemByFieldName()
    {
        $dataItem = $this->createDataItem();

        $dataRow = new DataRow();
        $dataRow->addDataItem($dataItem);

        $dataItemRetrieved = $dataRow->getDataItemByFieldName($dataItem->fieldName);

        $this->assertEquals($dataItemRetrieved, $dataItem);
    }

    public function testKeyFieldsPreparation()
    {
        $dataItem = $this->createDataItem();

        $dataRow = new DataRow();
        $dataRow->addDataItem($dataItem);

        $keyFields = [$dataItem->fieldName];
        $fieldMap = [];
        $dataItemManipulator = function () {};

        $dataRow->prepare($keyFields, $fieldMap, $dataItemManipulator);

        $dataItemRetrieved = $dataRow->getDataItemByFieldName($dataItem->fieldName);

        $this->assertTrue($dataItemRetrieved->keyField);
    }

    public function testFieldMapPreparation()
    {
        $dataItem = $this->createDataItem();

        $dataRow = new DataRow();
        $dataRow->addDataItem($dataItem);

        $faker = Faker\Factory::create();
        $newFieldName = $faker->word;

        $keyFields = [];
        $fieldMap = [$dataItem->fieldName => $newFieldName];
        $dataItemManipulator = function () {};

        $dataRow->prepare($keyFields, $fieldMap, $dataItemManipulator);

        $dataItemRetrieved = $dataRow->getDataItemByFieldName($dataItem->fieldName);

        $this->assertEquals($dataItemRetrieved->fieldName, $newFieldName);
    }

    public function testDataItemManipulatorPreparation()
    {
        $dataItem = $this->createDataItem();
        $oldValue = $dataItem->value;

        $dataRow = new DataRow();
        $dataRow->addDataItem($dataItem);

        $keyFields = [];
        $fieldMap = [];
        $dataItemManipulator = function ($dataItemToManipulate) use ($dataItem) {
            if ($dataItemToManipulate->fieldName == $dataItem->fieldName) {
                $dataItemToManipulate->value = strrev($dataItemToManipulate->value);
            }            
        };

        $dataRow->prepare($keyFields, $fieldMap, $dataItemManipulator);

        $dataItemRetrieved = $dataRow->getDataItemByFieldName($dataItem->fieldName);

        $this->assertEquals(strrev($oldValue), $dataItemRetrieved->value);
    }

}
