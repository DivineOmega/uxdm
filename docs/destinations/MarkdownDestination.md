# Markdown Destination

The UXDM Markdown destination allows you to migrate data into a Markdown table.

## Creating

To create a new Markdown destination, you must provide it with the file path of Markdown file you wish to export data to. The 
first line of the Markdown table will contain the field names.

The following example creates a Markdown destination object, using a Markdown file called `users.md` in the same directory.

```php
$markdownFile = __DIR__.'/users.md';
$markdownDestination = new MarkdownDestination($markdownFile);
```

## Assigning to migrator

To use the Markdown destination as part of a UXDM migration, you must assign it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($markdownDestination);
```

Alternatively, you can add multiple destinations, as shown below. You can also specify the fields you wish to send to each destination by 
passing an array of field names as the second parameter.

```php
$migrator = new Migrator;
$migrator->addDestination($markdownDestination, ['field1', 'field2']);
$migrator->addDestination($otherDestination, ['field3', 'field2']);
```