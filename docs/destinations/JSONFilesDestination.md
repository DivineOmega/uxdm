# JSON Files destination

The UXDM JSON files destination allows you to export data to multiple JSON files - each one representing a single record containing multiple fields.

## Creating

To create a new JSON files destination, you must provide it with the file path of the directory you wish the JSON files to be created in. 
The JSON files will be named `1.json`, `2.json`, `3.json`, and so on. Any existing JSON files in the specified directory that match this format will 
be overwritten.

The following example creates a JSON files destination object, using a subdirectory called `users` in the same directory.

```php
$jsonDirectory = __DIR__.'/users/';
$jsonFilesDestination = new JSONFilesDestination($jsonDirectory);
```

## Assigning to migrator

To use the JSON files destination as part of a UXDM migration, you must assign it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($jsonFilesDestination);
```

Alternatively, you can add multiple destinations, as shown below. You can also specify the fields you wish to send to each destination by 
passing an array of field names as the second parameter.

```php
$migrator = new Migrator;
$migrator->addDestination($jsonFilesDestination, ['field1', 'field2']);
$migrator->addDestination($otherDestination, ['field3', 'field2']);
```