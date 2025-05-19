# NDJSON Source

The UXDM NDJSON source allows you to read data from a newline delimited JSON file where each line contains a single JSON object.

## Creating

Provide the NDJSON file path when creating the source.

```php
$ndjsonFile = __DIR__.'/users.ndjson';
$ndjsonSource = new NDJSONSource($ndjsonFile);
```

## Assigning to migrator

Assign the NDJSON source to the migrator as you would any other source.

```php
$migrator = new Migrator;
$migrator->setSource($ndjsonSource);
```
