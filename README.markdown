
Overview
========
This is an implemention of a REST API in Zend Framework (version 1.x).

There are several reasons this codebase was created.  Among them are:

	* Support versioning in URIs (reasoning behind this can be found at http://www.notmessenger.com/rest/3-tenets-for-implementing-a-rest-api/)
	* Resource hierarchies such as '/customer/12/orders/3'
	* Automated creation of representations in responses 

This codebase had to be created because the Zend_Rest module currently does not support any of these needs, or at least not in a way that maintains one's sanity.

For an explanation of all of the code please read the explanatory blog post at http://www.notmessenger.com/TBD (post has not yet been published)

Requirements
============

 * PHP 5.3.2 or greater
 * short_open_tag = 1 if PHP < 5.4.0 otherwise need to replace '<?=' with '<?php echo' in the view scripts and layouts
 * Database server
 * APC (optional, but have to make configuration changes if not using) 

Configuration
=============

As this code is presented it assumes the use of APC and MySQL (available on 'localhost').

Remove APC support
------------------

If you do not wish to use APC remove line 30

	<service id="doctrine.orm.cache.apc" class="Doctrine\Common\Cache\ApcCache" />

from /config/di/doctrine/orm.xml

Use a database other than MySQL
-------------------------------

If you do not wish to use MySQL replace the value of the "db.driver" parameter

	<parameter key="db.driver">pdo_mysql</parameter>

on line 8 of the /config/db.xml file with the appropriate driver of the database you are using.

Receive emails about errors that occur in Production
----------------------------------------------------

If you would like to receive emails about any errors that occur in Production then be sure to set a value for the 'appSettings.logger.email' directive in /config/application.ini

Initial Setup
=============

Set 'path.project' value
------------------------
Line 13 of the /config/di/services.xml file, which looks like below

	<parameter key="path.project">/usr/www/api-demo</parameter>

needs to have its value set to the correct Document Root of this application's installation.

Database configuration and credentials
--------------------------------------
The /config/db.xml file contains the parameters you need to provide values for in order to configure your database connection.

Creating needed database tables
-------------------------------
To create the database tables required to fully experience this code's functionality, run the following commands:

<pre>
> cd bin/doctrine
> ./doctrine orm:schema-tool:create
</pre>
