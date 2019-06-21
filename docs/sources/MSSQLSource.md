# MSSQL Source

The UXDM MSSQL source allows you to source data from one or more Microsoft SQL database tables.

This source is needed due to various non-standard Microsoft SQL syntax.

## Creating

To create a new MSSQL source, you must provide it with a PDO object and the name of the table
you wish to source data from. 

The following example creates a new PDO object for a `test` database on the localhost with username `root` and password `password123`. It then creates a PDO source object, using the newly created PDO object and the table name `users` to source data from.

```php
$pdo = new PDO('dblib:version=7.0;charset=UTF-8;host=127.0.0.1;dbname=test', 'username', 'password123');
$mssqlSource = new MSSQLSource($pdo, 'users');
```

## Other usages

Use of this source is mostly identical to the PDO Source.

See the [PDO Source documentation](PDOSource.md) for further usage instructions.