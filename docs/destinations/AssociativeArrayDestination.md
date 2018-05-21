# Associative Array Destination

The UXDM associative array destination allows you to export data to an associative array.

## Creating

To create a new associative array destination, you must provide it with an empty array that data can be exported to.

The following example creates a associative array destination object.

```php
$destinationArray = [];
$associativeArrayDestination = new AssociativeArrayDestination($destinationArray);
```

After migration, the `$destinationArray` will contain the exported data, similar to the following.

```php
[
    ['name' => 'Worf', 'role' => 'security'],
    ['name' => 'Picard', 'role' => 'captain'],
    ['name' => 'Data', 'role' => 'second-officer'],
    ['name' => 'Riker', 'role' => 'first-officer']
];
```

You can then loop through this data and access it, as shown in the example below..

```php
foreach($destinationArray as $row) {
    echo 'Crew member '.$row->name.' has the role of '.$row->role.'.'.PHP_EOL;
}
```

## Assigning to migrator

To use the associative array destination as part of a UXDM migration, you must assign it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($associativeArrayDestination);
```

Alternatively, you can add multiple destinations, as shown below. You can also specify the fields you wish to send to each destination by 
passing an array of field names as the second parameter.

```php
$migrator = new Migrator;
$migrator->addDestination($associativeArrayDestination, ['field1', 'field2']);
$migrator->addDestination($otherDestination, ['field3', 'field2']);
```
