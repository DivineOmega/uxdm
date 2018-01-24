<?php

require_once __DIR__.'/../../../vendor/autoload.php';

use RapidWeb\uxdm\Objects\Destinations\CSVDestination;
use RapidWeb\uxdm\Objects\Migrator;
use RapidWeb\uxdm\Objects\Sources\XMLSource;

$xmlSource = new XMLSource(__DIR__.'/source.xml', '/ns:urlset/ns:url');
$xmlSource->addXMLNamespace('ns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

$csvDestination = new CSVDestination(__DIR__.'/destination.csv');

$migrator = new Migrator();
$migrator->setSource($xmlSource)
         ->setDestination($csvDestination)
         ->setFieldsToMigrate(['loc'])
         ->migrate();
