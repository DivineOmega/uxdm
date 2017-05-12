<?php

require_once __DIR__.'/../../../vendor/autoload.php';

use RapidWeb\uxdm\Objects\Sources\PDOSource;
use RapidWeb\uxdm\Objects\Destinations\CSVDestination;
use RapidWeb\uxdm\Objects\Migrator;

$pdo = new PDO('mysql:dbname=laravel-test;host=127.0.0.1', 'root', getenv('UXDM_EXAMPLE_PASSWORD'));
$pdoSource = new PDOSource($pdo, 'users');
$csvDestination = new CSVDestination(__DIR__.'/destination.csv');

$migrator = new Migrator;
$migrator->setSource($pdoSource)
         ->setDestination($csvDestination)
         ->migrate();