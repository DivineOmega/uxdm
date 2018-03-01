# XML Destination

The UXDM XML destination allows you to migrate data into an XML file.

## Creating

To create a new XML destination, you must provide it with the file path of XML file you wish to export data to, 
a `DOMDocument` object, an attached `DOMElement` which will contain your data rows, and (optionally) a name for element 
that will be created for each row.

The following example creates a XML destination object, using a XML file called `users.xml` in the same directory.

```php
$xmlFile = __DIR__.'/users.xml';
$domDoc = new DOMDocument();
$rootElement = $domDoc->appendChild(new DOMElement('users'));
$perRowElementName = 'user';
$xmlDestination = new XMLDestination($xmlFile, $domDoc, $rootElement, $perRowElementName);
```

This would create a file with a syntax similar to the following.

```xml
<?xml version="1.0"?>
<users>
    <user>
        <name>voluptas</name>
        <password>844abd!#</password>
    </user>
    <user>
        <name>necessitatibus</name>
        <password>drkl724^s</password>
    </user>
</users>
```

## Assigning to migrator

To use the XML destination as part of a UXDM migration, you must assign it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($xmlDestination);
```

Alternatively, you can add multiple destinations, as shown below. You can also specify the fields you wish to send to each destination by 
passing an array of field names as the second parameter.

```php
$migrator = new Migrator;
$migrator->addDestination($xmlDestination, ['field1', 'field2']);
$migrator->addDestination($otherDestination, ['field3', 'field2']);
```
