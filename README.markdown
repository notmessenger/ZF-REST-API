
Overview
========



Requirements
============

 * PHP 5.3.2 or greater
 * Database server
 * APC (optional, but have to make configuration changes if not using) 

Configuration
=============

As this code is presented it assumes the use of APC and MySQL (available on 'localhost').

Remove APC support
------------------

If you do not wish to use APC remove line 31

	<pre>
	&lt;service id="doctrine.orm.cache.apc" class="Doctrine\Common\Cache\ApcCache" /&gt;
	</pre>

from /config/di/doctrine/orm.xml

Use a database other than MySQL
-------------------------------

If you do not wish to use MySQL replace the value of the "db.driver" parameter

	<pre>
	&lt;parameter key="db.driver"&gt;pdo_mysql&lt;/parameter&gt;
	</pre>

on line 8 of the /config/db.xml file with the appropriate driver of the database you are using.

Initial Setup
=============

Database configuration and credentials
--------------------------------------
The /config/db.xml file contains the parameters you need to provide values for in order to configure your database connection.

Creating of needed database tables
----------------------------------


