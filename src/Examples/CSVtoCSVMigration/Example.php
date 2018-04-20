<?php

require_once __DIR__.'/../../../vendor/autoload.php';

use DivineOmega\uxdm\Objects\Destinations\CSVDestination;
use DivineOmega\uxdm\Objects\Migrator;
use DivineOmega\uxdm\Objects\Sources\CSVSource;

$csvSource = new CSVSource(__DIR__.'/source.csv');
$csvDestination = new CSVDestination(__DIR__.'/destination.csv');

$migrator = new Migrator();
$migrator->setSource($csvSource)
         ->setDestination($csvDestination)
         ->setFieldsToMigrate(['Author'])
         ->setFieldMap(['Author' => 'Writer'])
         ->withProgressBar()
         ->migrate();
