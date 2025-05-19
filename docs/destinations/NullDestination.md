# Null Destination

The UXDM null destination does nothing with the migration data. This can be useful if you do not wish to migrate data,
but just wish to make use UXDM's data row manipulator and/or data item manipulator callbacks.

## Creating

The following example creates a null destination object.

```php
$nullDestination = new NullDestination($destinationArray);
```

## Assigning to migrator

To use the null destination as part of a UXDM migration, you must assign it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($nullDestination);
```
