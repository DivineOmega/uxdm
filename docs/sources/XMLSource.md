# XML Source

The UXDM XML source allows you to source data from an XML file. Since many systems contain export
tools that can output data in XML format, this source can be very useful for indirectly sourcing data from external 
systems.

## Creating

To create a new XML source, you must provide it with the file path of XML file, and the xPath query indicating where to
find the data in the file.

Consider the following example file.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<books>
  <book>
       <name>The Amazing Adventures of Jordan Hall</name>
       <author>Jordan Hall</author>
  </book>
  <book>
       <name>The Terrible Troubles of Mr Bunny Rabbit</name>
       <author>Sir Bunnyton</author>
  </book>
</books>
```

The code below creates an XML source object, using this XML file in the same directory, an xPath query that specifies
that the `book` sections should be retrieved, and specifications of the required namespaces.

```php
$xmlFile = __DIR__.'/books.xml';
$xpathQuery = '/books/book';
$xmlSource = new XMLSource($xmlFile, $xpathQuery);
```

## Assigning to migrator

To use the XML source as part of a UXDM migration, you must assign it to the migrator. This process is the same for most sources.

```php
$migrator = new Migrator;
$migrator->setSource($xmlSource);
```

## Using XML namespaces

If your XML file uses XML namespaces, you must add them to the XML source.

For example, consider the following snippet from an XML sitemap.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xmlns:xhtml="http://www.w3.org/1999/xhtml"
      xsi:schemaLocation="
            http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
  <url>
       <loc>https://www.rapidweb.biz/</loc>
       <lastmod>2017-06-05T23:08:55+00:00</lastmod>
       <changefreq>daily</changefreq>
       <priority>1.0000</priority>
  </url>
  <url>
       <loc>https://www.rapidweb.biz/web-design.html</loc>
       <lastmod>2017-06-05T23:08:55+00:00</lastmod>
       <changefreq>daily</changefreq>
       <priority>0.8000</priority>
  </url>
</urlset>
```

This sitemap defines a namespace of `http://www.sitemaps.org/schemas/sitemap/0.9`. This can be added as `ns` using
the XML source's `addXMLNamespace` method, as shown below. Note also that the xPath query is also modified to
include the namespace.

```php
$xmlSource = new XMLSource(__DIR__.'/source.xml', '/ns:urlset/ns:url');
$xmlSource->addXMLNamespace('ns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
```