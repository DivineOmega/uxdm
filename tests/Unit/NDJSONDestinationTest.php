<?php

use DivineOmega\uxdm\Objects\DataItem;
use DivineOmega\uxdm\Objects\DataRow;
use DivineOmega\uxdm\Objects\Destinations\NDJSONDestination;
use PHPUnit\Framework\TestCase;

final class NDJSONDestinationTest extends TestCase
{
    private function createDataRows()
    {
        $faker = Faker\Factory::create();

        $dataRows = [];

        $dataRow = new DataRow();
        $dataRow->addDataItem(new DataItem('name', $faker->word));
        $dataRow->addDataItem(new DataItem('value', $faker->randomNumber));
        $dataRow->addDataItem(new DataItem('nested.word', $faker->word));
        $dataRow->addDataItem(new DataItem('array.0', $faker->word));
        $dataRow->addDataItem(new DataItem('array.1', $faker->word));
        $dataRow->addDataItem(new DataItem('array.2', $faker->word));
        $dataRow->addDataItem(new DataItem('nested.array.0', $faker->word));
        $dataRow->addDataItem(new DataItem('nested.array.1', $faker->word));
        $dataRow->addDataItem(new DataItem('nested.array.2', $faker->word));
        $dataRows[] = $dataRow;

        $dataRow = new DataRow();
        $dataRow->addDataItem(new DataItem('name', $faker->word));
        $dataRow->addDataItem(new DataItem('value', $faker->randomNumber));
        $dataRow->addDataItem(new DataItem('nested.word', $faker->word));
        $dataRow->addDataItem(new DataItem('array.0', $faker->word));
        $dataRow->addDataItem(new DataItem('array.1', $faker->word));
        $dataRow->addDataItem(new DataItem('array.2', $faker->word));
        $dataRow->addDataItem(new DataItem('nested.array.0', $faker->word));
        $dataRow->addDataItem(new DataItem('nested.array.1', $faker->word));
        $dataRow->addDataItem(new DataItem('nested.array.2', $faker->word));
        $dataRows[] = $dataRow;

        return $dataRows;
    }

    private function getExpectedFileContent(array $dataRows)
    {
        $expected = '';
        foreach ($dataRows as $dataRow) {
            $row = [];
            foreach ($dataRow->getDataItems() as $dataItem) {
                $row[$dataItem->fieldName] = $dataItem->value;
            }
            $expected .= json_encode(array_undot($row)) . PHP_EOL;
        }

        return $expected;
    }

    public function testPutDataRows()
    {
        $dataRows = $this->createDataRows();

        $file = __DIR__.'/Data/destination.ndjson';
        $destination = new NDJSONDestination($file);
        $destination->putDataRows($dataRows);
        $destination->finishMigration();

        $fileContent = file_get_contents($file);

        $this->assertEquals($this->getExpectedFileContent($dataRows), $fileContent);
    }
}
