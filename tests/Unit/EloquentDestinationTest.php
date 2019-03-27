<?php

use DivineOmega\uxdm\Objects\DataItem;
use DivineOmega\uxdm\Objects\DataRow;
use DivineOmega\uxdm\Objects\Destinations\EloquentDestination;
use DivineOmega\uxdm\TestClasses\Eloquent\User;
use PHPUnit\Framework\TestCase;

final class EloquentDestinationTest extends TestCase
{
    private $pdo = null;

    private function getDestination()
    {
        $this->pdo = new PDO('sqlite:'.__DIR__.'/Data/destination.sqlite');

        $sql = 'DROP TABLE IF EXISTS users';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();


        $sql = 'CREATE TABLE IF NOT EXISTS users (id integer primary key autoincrement, name TEXT, value INTEGER)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        require_once 'includes/BootDestinationEloquentDatabase.php';

        return new EloquentDestination(User::class);
    }

    private function createDataRows()
    {
        $faker = Faker\Factory::create();

        $dataRows = [];

        $dataRow = new DataRow();
        $dataRow->addDataItem(new DataItem('name', $faker->word, true));
        $dataRow->addDataItem(new DataItem('value', $faker->randomNumber));
        $dataRows[] = $dataRow;

        $dataRow = new DataRow();
        $dataRow->addDataItem(new DataItem('name', $faker->word, true));
        $dataRow->addDataItem(new DataItem('value', $faker->randomNumber));
        $dataRows[] = $dataRow;

        return $dataRows;
    }

    private function alterDataRows(array $dataRows)
    {
        $faker = Faker\Factory::create();

        foreach ($dataRows as $dataRow) {
            $dataItem = $dataRow->getDataItemByFieldName('value');
            $dataItem->value = $faker->randomNumber;
        }

        return $dataRows;
    }

    private function getActualArray()
    {
        $sql = 'SELECT name, value FROM users';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    private function getExpectedArray(array $dataRows)
    {
        $expectedArray = [];

        foreach ($dataRows as $dataRow) {
            $expectedArrayRow = [];
            foreach ($dataRow->getDataItems() as $dataItem) {
                $expectedArrayRow[$dataItem->fieldName] = $dataItem->value;
            }
            $expectedArray[] = $expectedArrayRow;
        }

        return $expectedArray;
    }

    public function testPutDataRows()
    {
        $dataRows = $this->createDataRows();

        $destination = $this->getDestination();

        $destination->putDataRows($dataRows);

        $this->assertEquals($this->getExpectedArray($dataRows), $this->getActualArray());

        $dataRows = $this->alterDataRows($dataRows);

        $destination->putDataRows($dataRows);

        $this->assertEquals($this->getExpectedArray($dataRows), $this->getActualArray());

        $destination->finishMigration();
    }
}
