# Universal Extensible Data Migrator (UXDM)

[![Build Status](https://travis-ci.org/rapidwebltd/uxdm.svg?branch=master)](https://travis-ci.org/rapidwebltd/uxdm)

Universal Extensible Data Migrator (UXDM) is a PHP package designed to help developers migrate data from one system/format to another.

# Migrations

Each UXDM migration requires a source object and at least one destination object. These determine how data is read or written. The UXDM package comes with a variety of source and destination objects, including the following.

* PDO (PHP Database Object) Source & Destination
* CSV (Comma Seperated Values) Source & Destination
* Associative Array Source & Destination
* XML Source
* Debug Output Destination

Source and destination objects can be used in any combination. Data can be migrated from a CSV and inserted into a database, just as easily as data can be migrated from a database to a CSV.

You can also use similar source and destination objects in the same migration. For example, a common use of UXDM is to use a PDO source and PDO destination to transfer data from one database to another.