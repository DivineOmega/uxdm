<?php

require_once __DIR__.'/../../../vendor/autoload.php';

use RapidWeb\uxdm\Objects\Sources\CSVSource;
use RapidWeb\uxdm\Objects\Migrator;

$csvSource = new CSVSource(__DIR__.'/source.csv');

$migrator = new Migrator;
$migrator->setSource($csvSource)->migrate();