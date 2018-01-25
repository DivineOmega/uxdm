# Debug Output Destination

The UXDM debug output destination outputs a dump of the data rows passed to it. This can be useful for debugging
UXDM migrations via the command line.

## Creating
The following example creates a debug output destination object.

```php
$debugOutputDestination = new DebugOutputDestination($destinationArray);
```

## Assigning to migrator

To use the debug output destination as part of a UXDM migration, you must assign it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($debugOutputDestination);
```

Alternatively, you can add multiple destinations, as shown below. You can also specify the fields you wish to send to each destination by 
passing an array of field names as the second parameter.

```php
$migrator = new Migrator;
$migrator->addDestination($debugOutputDestination, ['field1', 'field2']);
$migrator->addDestination($otherDestination, ['field3', 'field2']);
```