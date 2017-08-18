<?php

use PHPUnit\Framework\TestCase;

use RapidWeb\uxdm\Objects\Migrator;
use RapidWeb\uxdm\Objects\DataItem;
use RapidWeb\uxdm\Objects\Sources\PDOSource;
use RapidWeb\uxdm\Objects\Destinations\PDODestination;

final class MigratorTest extends TestCase
{
    private $pdo;
    private $pdo2;

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

        $sql = 'CREATE TABLE IF NOT EXISTS migrator_test (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, md5_name TEXT, email_address TEXT)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return new PDODestination($this->pdo, 'migrator_test');
    }

    private function getPDODestination2()
    {
        $this->pdo2 = new PDO('sqlite::memory:');

        $sql = 'DROP TABLE IF EXISTS migrator_test2';
        $stmt = $this->pdo2->prepare($sql);
        $stmt->execute();

        $sql = 'CREATE TABLE IF NOT EXISTS migrator_test2 (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT)';
        $stmt = $this->pdo2->prepare($sql);
        $stmt->execute();

        return new PDODestination($this->pdo2, 'migrator_test2');
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
            'md5_name' => 'e699d5afb08b7a16fb4e9c707353fe48',
            'email_address' => 'bear@example.com'
        ];
        return $expected;
    }

    private function getActualArray2()
    {
        $sql = 'SELECT * FROM migrator_test2';
        $stmt = $this->pdo2->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    private function getExpectedArray2()
    {
        $expected = [];
        $expected[0] = [
            'id' => 2,
            'name' => 'BEAR'
        ];
        return $expected;
    }

    public function testMigrator()
    {
        $migrator = new Migrator;

        $migrator->setSource($this->getPDOSource())
                 ->setDestination($this->getPDODestination())
                 ->setFieldsToMigrate(['id', 'name', 'email'])
                 ->setKeyFields(['id'])
                 ->setFieldMap(['email' => 'email_address'])
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

    public function testMigratorWithNoSource()
    {
        $this->expectException(Exception::class);

        $migrator = new Migrator;
        $migrator->migrate();

    }

    public function testMigratorWithNoDestination()
    {
        $this->expectException(Exception::class);

        $migrator = new Migrator;
        $migrator->setSource($this->getPDOSource())
                 ->migrate();

    }

    public function testMigratorWithNoFieldsToMigrate()
    {
        $migrator = new Migrator;

        $migrator->setSource($this->getPDOSource())
                 ->setDestination($this->getPDODestination())
                 ->setKeyFields(['id'])
                 ->setFieldMap(['email' => 'email_address'])
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

    public function testMigratorWithMultipleDestinations()
    {
        $migrator = new Migrator;

        $migrator->setSource($this->getPDOSource())
                 ->addDestination($this->getPDODestination())
                 ->addDestination($this->getPDODestination2(), ['id', 'name'])
                 ->setFieldsToMigrate(['id', 'name', 'email'])
                 ->setKeyFields(['id'])
                 ->setFieldMap(['email' => 'email_address'])
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
        $this->assertEquals($this->getExpectedArray2(), $this->getActualArray2());
    }

}
