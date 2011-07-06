
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
	<service id="doctrine.orm.cache.apc" class="Doctrine\Common\Cache\ApcCache" />
	</pre>

from /config/di/doctrine/orm.xml

Use database other than MySQL
-----------------------------

If you do not wish to use MySQL replace the value of the "db.driver" parameter

	<pre>
	<parameter key="db.driver">pdo_mysql</parameter>
	</pre>

on line 8 of the /config/db.xml file with the appropriate driver of the database you are using.

 
Initial Setup
=============


