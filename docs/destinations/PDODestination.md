# PDO Destination

The UXDM PDO destination allows you to migrate data into a database table.

## Creating

To create a new PDO destination, you must provide it with a PDO object and the name of the table
you wish to export data to. 

The following example creates a new PDO object for a `test` database on the localhost with username `root` and password `password123`. It then creates a PDO destination object, using the newly created PDO object and the table name `users` to export data to.

```php
$pdo = new PDO('mysql:dbname=test;host=127.0.0.1', 'root', 'password123');
$pdoDestination = new PDODestination($pdo, 'users');
```

## Assigning to migrator

To use the PDO destination as part of a UXDM migration, you must assign it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($pdoDestination);
```

Alternatively, you can add multiple destinations, as shown below. You can also specify the fields you wish to send to each destination by 
passing an array of field names as the second parameter. This can be used to export data to multiple different database tables in one migration.

```php
$migrator = new Migrator;
$migrator->addDestination($pdoDestination, ['field1', 'field2']);
$migrator->addDestination($otherDestination, ['field3', 'field2']);
```

# Integrity constraint violations

When exporting data using a PDO destination, the database management system may report integrity constraint violations if existing exported
records already exist and/or there are index conflicts. By default, an integrity constraint violation would cause the migration to halt. 
If you wish to ignore integrity constraint violations, you do this easily, as shown below.

```php
$pdoDestination->ignoreIntegrityConstraintViolations();
```

# Transactions

By default, database transactions are enabled. This helps speed up the migration process by keeping changes in memory until they are committed to disk. If you do not want to use transactions or they not supported by your database engine, you can easily disable them, as shown below.

```php
$pdoDestination->disableTransactions();
```
