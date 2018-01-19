# CSV Destination

The UXDM CSV destination allows you to migrate data into a comma seperated file (CSV).

## Creating

To create a new CSV destination, you must provide it with the file path of CSV file you wish to export data to. The 
first line of the CSV file will contain the field names.

The following example creates a CSV destination object, using a CSV file called `users.csv` in the same directory.

```php
$csvFile = __DIR__.'/users.csv';
$csvDestination = new csvDestination($csvFile);
```

## Assigning to migrator

To use the CSV destination as part of a UXDM migration, you must assign it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($csvDestination);
```

Alternatively, you can add multiple destinations, as shown below. You can also specify the fields you wish to send to each destination by 
passing an array of field names as the second parameter.

```php
$migrator = new Migrator;
$migrator->addDestination($csvDestination, ['field1', 'field2']);
$migrator->addDestination($otherDestination, ['field3', 'field2']);
```