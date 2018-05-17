# Eloquent Source

The UXDM Eloquent source allows you to source data from an Eloquent model. This can be handy if you need to migrate data
from a system using the Eloquent ORM, such as a Laravel project.

## Creating

To create a new Eloquent source, you must provide it with the class name of Eloquent model you wish to use.

The following example creates a Eloquent source object, using an Eloquent model called `User` in the `App` namespace.

```php
$eloquentSource = new EloquentSource(\App\User::class);
```

## Assigning to migrator

To use the Eloquent source as part of a UXDM migration, you must assign it to the migrator. This process is the same for most sources.

```php
$migrator = new Migrator;
$migrator->setSource($eloquentSource);
```
