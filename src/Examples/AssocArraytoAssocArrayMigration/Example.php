<?php

require_once __DIR__.'/../../../vendor/autoload.php';

use RapidWeb\uxdm\Objects\Sources\AssociativeArraySource;
use RapidWeb\uxdm\Objects\Destinations\AssociativeArrayDestination;
use RapidWeb\uxdm\Objects\Migrator;

$sourceArray = [
    ['name' => 'James', 'height' => 1.88],
    ['name' => 'Frank', 'height' => 1.73]
];
$assocArraySource = new AssociativeArraySource($sourceArray);

$destinationArray = [];
$assocArrayDestination = new AssociativeArrayDestination($destinationArray);

$migrator = new Migrator;
$migrator->setSource($assocArraySource)
         ->setDestination($assocArrayDestination)
         ->migrate();

var_dump($destinationArray);