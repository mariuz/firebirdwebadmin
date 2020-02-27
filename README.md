[![npm version](https://badge.fury.io/js/firebird-web-admin.svg)](https://badge.fury.io/js/firebird-web-admin)
[![Crowdin](https://d322cqt584bo4o.cloudfront.net/firebirdwebadmin/localized.svg)](https://crowdin.com/project/firebirdwebadmin)
[![Code Climate](https://codeclimate.com/github/mariuz/firebirdwebadmin/badges/gpa.svg)](https://codeclimate.com/github/mariuz/firebirdwebadmin)

# FirebirdWebAdmin is a web frontend for the Firebird database server.

By now it has the functionalities for
  
* creating, deleting, modifying databases, tables, generators, views, triggers, domains, indices, stored procedures, udf's,     exceptions, roles and database users
* performing sql expressions on databases and display the results
* import and export of data through files in the csv format
* browsing through the contents of tables and views, watching them growing while typing in data
* selecting data for deleting and editing while browsing tables
* inserting, deleting, displaying the contents of blob fields
* diplaying database metadata, browsing the firebird system tables
* database backup and restore, database maintenance

Some of the features are only available if the database- and the web-server are running on the same machine. The reason is that php   have to call the Firebird tools (isql, gsec, gstat, etc.) to perform certain actions.

## Overview
1. [Documentation](#documentation)
2. [Requirements](#requirements)
3. [ChangeLog](#requirements)
4. [Contributing](#contributing)
5. [Copyright notice](#copyright-notice)

## Documentation

There is no documentation available yet, but if you are familiar with Firebird  you will have no troubles using FirebirdWebAdmin.

For some basic configuration settings have a look to the file `./inc/configuration.inc.php` before you start the programm.

Here is how to use and install on Ubuntu https://help.ubuntu.com/community/Firebird2.5

Firebird documentation is located on this page http://www.firebirdsql.org/en/documentation/

## Requirements

This is the environment I'm using for the development. Other components are not or less tested. So if you got problems make sure you are not using older software components.

php5.x with compiled in support for Firebird/InterBase and pcre (but any version >= 5.3.x should work)

Firebird 2.x.x for Linux
        
Apache 2.x or lighttpd or nginx

## ChangeLog
#### Version 3.4.1 (27.02.2020)
- [enhancement:] Adjust "Accessories" page UI.
- [enhancement:] Remove Crowdin badge from footer.
- [enhancement:] Update debug_funcs.inc.php
- [bugfix:] Don't warn if "isql" is "isql-fb" on Linux
- [typo:] Correct typo: firebirid -> firebird
- [bugfix] fix sql create database
- [enhancement:] Add Character Sets
- [enhancement:] Quiet PHP7.2 deprecation warning …
- [enhancement:] Further create_function refactor
- [enhancement:] Remove unused/outdated markableFbwaTable.
- [enhancement:] cosmetics

#### Further informations
- See [CHANGELOG.md][changelog] to get the full changelog.

## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request

## Copyright notice

 (C) 2000,2001,2002,2003,2004 Lutz Brueckner <irie@gmx.de>
                              Kapellenstr. 1A
                              22117 Hamburg, Germany

FirebirdWebAdmin is published under the terms of the [GNU GPL v.2][gnu_gpl_v2_license], please read the file LICENCE for details.

This software is provided 'as-is', without any expressed or implied warranty.  In no event will the author be held liable for any damages arising from the use of this software.

[gnu_gpl_v2_license]: https://opensource.org/licenses/GPL-2.0
[changelog]: CHANGELOG.md
