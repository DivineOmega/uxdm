<?php

use PHPUnit\Framework\TestCase;

use RapidWeb\uxdm\Objects\DataRow;
use RapidWeb\uxdm\Objects\DataItem;
use RapidWeb\uxdm\Objects\Destinations\DebugOutputDestination;

final class DebugOutputDestinationTest extends TestCase
{
    private function createDataRows()
    {
        $faker = Faker\Factory::create();

        $dataRows = [];

        $dataRow = new DataRow;
        $dataRow->addDataItem(new DataItem('name', $faker->word));
        $dataRow->addDataItem(new DataItem('value', $faker->randomNumber));
        $dataRows[] = $dataRow;

        $dataRow = new DataRow;
        $dataRow->addDataItem(new DataItem('name', $faker->word));
        $dataRow->addDataItem(new DataItem('value', $faker->randomNumber));
        $dataRows[] = $dataRow;

        return $dataRows;
    }

    public function testPutDataRows()
    {
        $dataRows = $this->createDataRows();

        $destination = new DebugOutputDestination();
        ob_start();
        $destination->putDataRows($dataRows);
        $output = ob_get_clean();

        ob_start();
        var_dump($dataRows);
        $expectedOutput = ob_get_clean();

        $this->assertEquals($expectedOutput, $output);        
    }

}
