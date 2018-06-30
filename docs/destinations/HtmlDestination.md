# HTML Destination

The UXDM HTML destination allows you to migrate data into a HTML table.

## Creating

To create a new HTML destination, you must provide it with the file path of HTML file you wish to export data to. The 
first row of the HTML table will contain the field names.

The following example creates a HTML destination object, using a HTML file called `users.html` in the same directory.

```php
$htmlFile = __DIR__.'/users.html';
$htmlDestination = new HtmlDestination($htmlFile);
```

## Assigning to migrator

To use the HTML destination as part of a UXDM migration, you must assign it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($htmlDestination);
```

Alternatively, you can add multiple destinations, as shown below. You can also specify the fields you wish to send to each destination by 
passing an array of field names as the second parameter.

```php
$migrator = new Migrator;
$migrator->addDestination($htmlDestination, ['field1', 'field2']);
$migrator->addDestination($otherDestination, ['field3', 'field2']);
```