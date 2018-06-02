# Eloquent Destination

The UXDM Eloquent destination allows you to migrate data into an Eloquent model. This can be handy if you need to migrate data
into a system using the Eloquent ORM, such as a Laravel project.

## Creating

To create a new Eloquent destination, you must provide it with the class name of Eloquent model you wish to use.

The following example creates a Eloquent destination object, using an Eloquent model called `User` in the `App` namespace.

```php
$eloquentDestination = new EloquentDestination(\App\User::class);
```

## Assigning to migrator

To use the Eloquent destination as part of a UXDM migration, you must assign it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($eloquentDestination);
```

Alternatively, you can add multiple destinations, as shown below. You can also specify the fields you wish to send to each destination by 
passing an array of field names as the second parameter.

```php
$migrator = new Migrator;
$migrator->addDestination($eloquentDestination, ['field1', 'field2']);
$migrator->addDestination($otherDestination, ['field3', 'field2']);
```