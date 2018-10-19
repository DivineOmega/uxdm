# PDF Destination

The UXDM PDF destination allows you to migrate data into a PDF table.

## Creating

To create a new PDF destination, you must provide it with the file path 
of PDF file you wish to export data to. The first row of the PDF table 
will contain the field names.

The following example creates a PDF destination object, using a PDF 
file called `users.pdf` in the same directory.

```php
$pdfFile = __DIR__.'/users.pdf';
$pdfDestination = new PDFDestination($pdfFile);
```

## Custom Paper Size & Orientation

You can alter the PDF destination to output PDFs with a custom paper 
size and orientation. To do this, use the `setPaperSize` and 
`setPaperOrientation` methods of the PDF destination object, as shown 
below. 

```php
$destination->setPaperSize('A5');
$destination->setPaperOrientation('landscape');
```

## HTML Prefix & Suffix

Internally, the PDF destination renders a basic HTML table into a PDF. 
If you wish to style the table, add a heading, or extra content, you can
add a HTML prefix or HTML suffix.

An example of how to do this is shown below.

```php
$htmlPrefix = '<h1>My Report</h1>
            <style>
                table { width: 100% }
                h1 { text-align: center; }
                th { text-transform: capitalize; text-align: center; } 
                th, td { margin: 0; border: 1px solid #000; }
            </style>';
$htmlSuffix = '<p>Created by UXDM</p>';

$destination->setHtmlPrefix($htmlPrefix);
$destination->setHtmlSuffix($htmlSuffix);
```

## Assigning to migrator

To use the PDF destination as part of a UXDM migration, you must assign 
it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($pdfDestination);
```

Alternatively, you can add multiple destinations, as shown below. You 
can also specify the fields you wish to send to each destination by 
passing an array of field names as the second parameter.

```php
$migrator = new Migrator;
$migrator->addDestination($PDFDestination, ['field1', 'field2']);
$migrator->addDestination($otherDestination, ['field3', 'field2']);
```