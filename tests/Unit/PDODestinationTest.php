<?php

use PHPUnit\Framework\TestCase;

use RapidWeb\uxdm\Objects\DataRow;
use RapidWeb\uxdm\Objects\DataItem;
use RapidWeb\uxdm\Objects\Destinations\PDODestination;

final class PDODestinationTest extends TestCase
{
    private $pdo = null;

    private function getPDODestination()
    {
        $this->pdo = new PDO('sqlite::memory:');

        $sql = 'DROP TABLE IF EXISTS pdo_destination_test';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $sql = 'CREATE TABLE IF NOT EXISTS pdo_destination_test (name TEXT, value INTEGER)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return new PDODestination($this->pdo, 'pdo_destination_test');
    }

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

        $destination = $this->getPDODestination();

        $destination->putDataRows($dataRows);

        $sql = 'SELECT * FROM pdo_destination_test';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $expectedArray = [];
        foreach($dataRows as $dataRow) {
            $expectedArrayRow = [];
            foreach($dataRow->getDataItems() as $dataItem) {
                $expectedArrayRow[$dataItem->fieldName] = $dataItem->value;
            }
            $expectedArray[] = $expectedArrayRow;
        }

        $this->assertEquals($expectedArray, $rows);        
    }

}
