# Doctrine Destination

The UXDM Doctrine destination allows you to migrate data into an Doctrine entity. This can be handy if you need to migrate data
into a system using the Doctrine ORM, such as a Symfony project.

## Creating

To create a new Doctrine destination, you must provide it with your Doctrine ORM EntityManager instance, and the class name of the Doctrine entity you wish to use.

The following example creates a Doctrine destination object, using a Doctrine entity called `User`. It is assumed that your `$entityManager` has already been created elsewhere.

```php
$doctrineDestination = new DoctrineDestination($entityManager, User::class);
```

## Assigning to migrator

To use the Doctrine destination as part of a UXDM migration, you must assign it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($doctrineDestination);
```

Alternatively, you can add multiple destinations, as shown below. You can also specify the fields you wish to send to each destination by 
passing an array of field names as the second parameter.

```php
$migrator = new Migrator;
$migrator->addDestination($doctrineDestination, ['field1', 'field2']);
$migrator->addDestination($otherDestination, ['field3', 'field2']);
```