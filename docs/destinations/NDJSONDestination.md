# NDJSON Destination

The UXDM NDJSON destination exports each data row to a single line within a newline delimited JSON file.

## Creating

To create a new NDJSON destination, provide it with the file path you wish the NDJSON data to be written to. Existing files will be overwritten.

```php
$ndjsonFile = __DIR__.'/users.ndjson';
$ndjsonDestination = new NDJSONDestination($ndjsonFile);
```

## Assigning to migrator

Assign the NDJSON destination to the migrator in the usual way.

```php
$migrator = new Migrator;
$migrator->setDestination($ndjsonDestination);
```

You may also add multiple destinations and specify the fields you wish to send to each destination using the second parameter.

```php
$migrator->addDestination($ndjsonDestination, ['field1', 'field2']);
```
