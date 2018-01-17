# JSON Files Source

The UXDM JSON files source allows you to source data from multiple JSON files - each one representing a single record containing multiple fields.

## Creating

To create a new JSON files source, you must provide it with an array of file paths to the JSON files file you wish to use.

Consider the following files.

**users/james.json**
```json
{
    "name": "James Matthews",
    "access_level": {
        "number": 5
    },
    "colours": [
        "red",
        "green"
    ]
}
```

**users/jenny.json**
```json
{
    "name": "Jenny Williams",
    "access_level": {
        "number": 6
    },
    "colours": [
        "blue",
        "yellow"
    ]
}
```

Deep fields and arrays are represented using dot notation, such as `access_level.number` and `colours.0`.

The following example creates a JSON files source object, using an array of file paths created by the PHP `glob` function.

```php
$filePaths = glob(__DIR__.'/users/*.json');
$jsonFilesSource = new JSONFilesSource($filePaths);
```

## Assigning to migrator

To use the JSON files source as part of a UXDM migration, you must assign it to the migrator. This process is the same for most sources.

```php
$migrator = new Migrator;
$migrator->setSource($jsonFilesSource);
```
