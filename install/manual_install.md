#Installing the Semantic Web CMS Ximdex (Manual steps)

Ximdex CMS basically requires a Linux machine, a Database server as MySQL (or MariaDB) and Apache Web Server with PHP.

The easiest way to install Ximdex is downloading the bash script `XIMDEX_INSTALL.sh` into a clean directory and run the script. Ximdex can also be found as TAR (or TGZ or zip) files, linux packages, virtual machines. The XIMDEX_INSTALL.sh script, additional flavours or old versions of Ximdex can be downloaded from http://www.ximdex.com

Install Ximdex using one of the following methods:

- **Assisted**: the `XIMDEX_INSTALL.sh` script will ask you for the the name of the instance, name of the Ximdex database, usernames and passwords, installation pahts, etc. During the last step, the script will create a script called `1.-MoveXimdexToDocRoot.sh` that will run with root privileges (via sudo) or, if you want to control which commands are executed, will suggests you to run the commands from a root console.
	- **Automatic**: the XIMDEX_INSTALL.sh script with '-a setupfile' option will make all steps automatically. A commented template setup is at install/templates/setup.conf.
	
- **Manual**: the guideline at install/manual_install.md provides instructions for unzipping the Ximdex instance, creating the database, assigning permissions, creating database users, parameterizing Ximdex, etc.

>We recommend the `Assisted Installation` through the Bash Script XIMDEX_INSTALL.sh because it is fully interactive, less prone to errors and let you decide how to run commands requiring a superuser.  

###Installation Requirements

*  Access to a Console or terminal with Telnet o SSH.
	*  The installer script will run as a non privileged user but final step will need root access to install the Ximdex instance into the Document Root of your web server and to set permissions. The installer will then ask you to "sudo" a script that is generated during installation. You can skip it and run it later to end the installation.
*  Permissions on file system in the directory (under a document root for your web server) where Ximdex will reside. Enough free space in the filesystem. See 'conf/diskspace.conf' file for further information.
*  Database: MySQL Server (>= 5.1) or MariaDB (>=5.5).
	*  And a DB user with permissions to create the Ximdex Database.
*  Apache2 web server.
	*  Apache modules: libapache2-mod-php5, apache-mpm-worker (recommended).
*  PHP (>= 5.2.5).
	*  and PHP modules: php5-xsl, php5-cli, php5-curl, php5-gd, php5-mysql, php-pear, php5-suhosin (recommended).
	*  To allow spelling check using Xedit (our wysiwyg XML editor): php5-enchant module.
*  Other packages: wget.
*  For the client side (with javascript and cookies enabled): Firefox (>=3.6), Google Chrome, Safari or Opera, ...
*  An internet connection (if you want to use the automatic suggestion system based on ontologies and annotations provided by XOWL module or for dynamic remote publishing your content in the cloud).
*  Postfix or Sendmail (if you want to use notification by mail as it's defined by default). Otherwise, configure your 'conf/mail.conf'

See http://www.ximdex.org/documentacion/requirements_en.html for further information.


##Manual Installation Steps

1. Download Ximdex package, tar file and expand it:
	```
  	tar zxvf ximdex.tgz .
  	```
	You should end with a directory (i.e.: Ximdex_v35) containing all the Ximdex files and directories.

2. Move it to your Web Server Document Root with the name you want (i.e.: myximdex)

	```
	mv Ximdex_v35 /var/www/myximdex
	```
	You may need superuser privileges to do that!

	So, in this example, 'myximdex' will be your Ximdex instance after installing it.

3. Connect to the DB server using superuser credentials (i.e.: root)

	```
	i.e.: mysql -h localhost -u root -p
	```
	>Change 'localhost' for the hostname of your DB server.

4. Once connected, create the DB for Ximdex CMS (i.e.: myximdexDB):
	```
	create database myximdexDB;
	```

5. Create the DB user that Ximdex will use:
	```
  	GRANT ALL PRIVILEGES  ON myximdexDB.* TO 'XIMDEX_DBUSER'@'localhost' IDENTIFIED BY 'XIMDEX_DBPASS' WITH GRANT OPTION; 
  	GRANT ALL PRIVILEGES  ON myximdexDB.* TO 'XIMDEX_DBUSER'@'%' IDENTIFIED BY 'XIMDEX_DBPASS' WITH GRANT OPTION; 
	```
	>Where XIMDEX_DBUSER will be the user of the Database stablishing the connections and XIMDEX_DBPASS its password. Please choose your own username and password for the DB.

6. Import Ximdex DB to your DB server
  	```
  	mysql myximdexDB -u root -p -h localhost /var/www/myximdex/install/ximdex_data/ximdex.sql
	```

7. Connect to the DB and update some rows to set Ximdex parameters:
	```
	mysql myximdexDB -u root -p
	# To connect to the DB

	UPDATE Config SET ConfigValue='http://HOST/myximdex' WHERE ConfigKEY='UrlRoot';
	# Where HOST is the server (and route) where Ximdex will be accessible (i.e.: http://ximdex.org/space/myximdex) 
  
	UPDATE Config SET ConfigValue='/PHYSICALPATH/myximdex' WHERE ConfigKEY='AppRoot';
  	#Where PHYSICALPATH is the Unix path where you moved Ximdex in Step 2 (i.e.: /var/www/myximdex)
  
  	UPDATE Users SET Login='ximdex' where IdUser = '301' 
	UPDATE Nodes SET Name='ximdex' where IdNode = '301'
  	# It creates the user 'ximdex' that will have admin powers
  	
  	UPDATE Users SET Pass=MD5('XIMDEX_ADMIN_PASS') where IdUser = '301' 
  	# Where XIMDEX_ADMIN_PASS is the password for the Ximdex user with administrative access (username ximdex).
  
 	UPDATE Config SET ConfigValue='en_US' WHERE ConfigKEY='locale';
 	# Establish the locale (other values are es_ES, ...)
  
  	UPDATE Config SET ConfigValue='NAMEIT' WHERE ConfigKEY='ximid'; 
  	# Where NAMEIT is the name for your Ximdex instance if you want to send statistical info or ask any question to support. 
	 ```
	
8. Copy the parameters file for Ximdex from its template:
	```
	cp /var/www/myximdex/install/templates/install-params.conf.php /var/www/myximdex/conf/
	```
	> Using the actual route for your Ximdex instance if it is not /var/www/myximdex

9. Edit it with your favourite text editor:
	```
	vi /var/www/myximdex/conf/install-params.conf.php	
	```
	Changing carefully the ##PARAMS## you will find in the file to the actual values. You should end with something like this:
	```
	...
	/* DATABASE_PARAMS (do not remove this comment, please) */
        $DBHOST = "localhost";
        $DBPORT = "3306";
        $DBUSER = "XIMDEX_DBUSER";
        $DBPASSWD = "XIMDEX_DBPASS";
        $DBNAME = "myximdexDB";



	/* XIMDEX_PARAMS (do not remove this comment, please) */
		  if (!defined('XIMDEX_TIMEZONE'))
			define("XIMDEX_TIMEZONE", "Europe/Madrid");

		  date_default_timezone_set(XIMDEX_TIMEZONE);

        $XIMDEX_ROOT_PATH = "/var/www/myximdex";

		  if (!defined('DEFAULT_LOCALE'))
			 define('DEFAULT_LOCALE', 'en_US');

	...
	```
	Please, pay attention to the values in this file and the values you have chosen for the target directory (i.e.: /var/www/myximdex) and the parameters stored in the DB!!
	
10. Set File Owners and Permissions:
	```
	cd /var/www
	chown -R www-data:www-data myximdex
	chmod g+s data
	chmod g+s logs
	```
	
	
11. Open Ximdex with your browser to end the installation (http://HOST/myximdex)

