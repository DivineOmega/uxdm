# WordPress Post Source

The UXDM WordPress post source allows you to source post data and post meta data from a WordPress database.

## Creating

To create a new WordPress post source, you must provide it with a PDO object that points towards the WordPress database. 

The following example creates a new WordPress post object for a `wordpress` database on the localhost with username `root` and password `password123`. 
It then creates a WordPress post source object, using the newly created PDO object.

```php
$pdo = new PDO('mysql:dbname=wordpress;host=127.0.0.1', 'root', 'password123');
$wordPressPostSource = new WordPressPostSource($pdo);
```

If you wish, you can also change the table prefix, as shown below. If not changed, it defaults to `wp_`.

```php
$wordPressPostSource->setTablePrefix('wp2_');
```

## Assigning to migrator

To use the WordPress post source as part of a UXDM migration, you must assign it to the migrator. This process is the same for most sources.

```php
$migrator = new Migrator;
$migrator->setSource($wordPressPostSource);
```

## Using a custom post type

By default the WordPress post source will only retrieve posts of type `post`. If you wish, you can retrieve a different post type, such as
`page` or `product`. To do so, just specify the custom post type as the second parameter when constructing the WordPress post source.

See the snippet below.

```php
$pdo = new PDO('mysql:dbname=wordpress;host=127.0.0.1', 'root', 'password123');
$wordPressPostSource = new WordPressPostSource($pdo, 'product');
```
