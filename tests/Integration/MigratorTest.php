<?php

use PHPUnit\Framework\TestCase;

use RapidWeb\uxdm\Objects\Migrator;
use RapidWeb\uxdm\Objects\DataItem;
use RapidWeb\uxdm\Objects\Sources\PDOSource;
use RapidWeb\uxdm\Objects\Destinations\PDODestination;

final class MigratorTest extends TestCase
{
    private $pdo;

    private function getPDOSource()
    {
        return new PDOSource(new PDO('sqlite:'.__DIR__.'/Data/source.sqlite'), 'users');
    }

    private function getPDODestination()
    {
        $this->pdo = new PDO('sqlite::memory:');

        $sql = 'DROP TABLE IF EXISTS migrator_test';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $sql = 'CREATE TABLE IF NOT EXISTS migrator_test (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, md5_name TEXT)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return new PDODestination($this->pdo, 'migrator_test');
    }

    private function getActualArray()
    {
        $sql = 'SELECT * FROM migrator_test';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    private function getExpectedArray()
    {
        $expected = [];
        $expected[0] = [
            'id' => 2,
            'name' => 'BEAR',
            'md5_name' => 'e699d5afb08b7a16fb4e9c707353fe48'
        ];
        return $expected;
    }

    public function testMigrator()
    {
        $migrator = new Migrator;

        $migrator->setSource($this->getPDOSource())
                 ->setDestination($this->getPDODestination())
                 ->setFieldsToMigrate(['id', 'name'])
                 ->setKeyFields(['id'])
                 ->setDataItemManipulator(function($dataItem) {
                    if ($dataItem->fieldName=='name') {
                        $dataItem->value = strtoupper($dataItem->value);
                    }
                 })
                 ->setDataRowManipulator(function($dataRow) {
                    $dataRow->addDataItem(new DataItem('md5_name', md5($dataRow->getDataItemByFieldName('name')->value)));
                 })
                 ->setSkipIfTrueCheck(function($dataRow) {
                    if ($dataRow->getDataItemByFieldName('name')->value=='TIM') {
                        return true;
                    }
                 })
                 ->migrate();

        $this->assertEquals($this->getExpectedArray(), $this->getActualArray());
    }

}
