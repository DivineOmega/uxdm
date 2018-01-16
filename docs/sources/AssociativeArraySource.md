# Associative Array Source

The UXDM associative array source allows you to source data from a simple multidimensional associative array.
It can be useful if you have already retrieved the required data by other means.

## Creating

To create a new associative array source, you must provide it with a associative array in a specific format. 
The example below shows the required format.

```php
$crew = [
    ['name' => 'Worf', 'role' => 'security'],
    ['name' => 'Picard', 'role' => 'captain'],
    ['name' => 'Data', 'role' => 'second-officer'],
    ['name' => 'Riker', 'role' => 'first-officer']
];
```

The following example creates a associative array source object, using the associative array defined above.

```php
$associativeArraySource = new AssociativeArraySource($crew);
```

## Assigning to migrator

To use the associative array source as part of a UXDM migration, you must assign it to the migrator. This process is the same for most sources.

```php
$migrator = new Migrator;
$migrator->setSource($associativeArraySource);
```
