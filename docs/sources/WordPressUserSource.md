# WordPress User Source

The UXDM WordPress user source allows you to source user data and post meta data from a WordPress database.

## Creating

To create a new WordPress user source, you must provide it with a PDO object that points towards the WordPress database. 

The following example creates a new WordPress user object for a `wordpress` database on the localhost with username `root` and password `password123`. 
It then creates a WordPress user source object, using the newly created PDO object.

```php
$pdo = new PDO('mysql:dbname=wordpress;host=127.0.0.1', 'root', 'password123');
$wordPressUserSource = new WordPressUserSource($pdo);
```

## Assigning to migrator

To use the WordPress user source as part of a UXDM migration, you must assign it to the migrator. This process is the same for most sources.

```php
$migrator = new Migrator;
$migrator->setSource($wordPressUserSource);
```
