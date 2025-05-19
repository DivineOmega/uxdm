# CSV Source

The UXDM CSV source allows you to source data from a comma separated file (CSV). Since many systems contain export
tools that can output data in CSV format, this source can be very useful for indirectly sourcing data from external 
systems.

## Creating

To create a new CSV source, you must provide it with the file path of the CSV file you wish to use. You must ensure the
first line of the CSV file contains the field names.

The following example creates a CSV source object, using a CSV file called `users.csv` in the same directory.

```php
$csvFile = __DIR__.'/users.csv';
$csvSource = new CSVSource($csvFile);
```

## Assigning to migrator

To use the CSV source as part of a UXDM migration, you must assign it to the migrator. This process is the same for most sources.

```php
$migrator = new Migrator;
$migrator->setSource($csvSource);
```
