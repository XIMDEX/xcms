#Installing the Semantic Web CMS Ximdex (assisted and manual methods)

Ximdex CMS basically requires a Linux machine, a Database server as MySQL (or MariaDB) and Apache Web Server with PHP.

The easiest way to install Ximdex CMS is described in INSTALLATION.md (with Docker or using the web installer) but if you want to fully control the process this guideline shows the steps.

Install Ximdex using one of the following methods:
- **Docker instance**: it is the easiest way to try Ximdex. It is described at INSTALLATION.md file.
- **Web Installer**: an easy way to fully configure and install Ximdex is by downloading it, setting file permissions and pointing your web browser to your Ximdex CMS to end its configuration. This method is the described at the INSTALLATION.md file.
- **Manual**: if you want to fully control the installation the section provides instructions for unzipping the Ximdex instance, creating the database, assigning permissions, creating database users, parameterizing Ximdex, etc.


###Installation Requirements
*  Access to a terminal with Telnet or SSH.
*  Permissions to create the directory where Ximdex CMS will be installed (under a document root for your web server) and enough free space in the filesystem. See 'conf/diskspace.conf' file for further information.
*  Database: MySQL Server (>= 5.1) or MariaDB (>=5.5) and a DB user that can create the Ximdex Database.
*  Apache2 web server with:
	*  Apache modules: libapache2-mod-php5, apache-mpm-worker (recommended).
*  PHP (>= 5.2.5).
	*  and PHP modules: php5-xsl, php5-cli, php5-curl, php5-gd, php5-mysql, php-pear, php5-suhosin (recommended).
	*  To use the spelling checker in Xedit (our wysiwyg XML editor): php5-enchant module.
*  Other packages: wget.
*  For the client side (with javascript and cookies enabled): Firefox (>=3.6), Google Chrome, Safari or Opera, ...
*  An internet connection if you want to use some features as the automatic suggestion system based on ontologies (XOWL module), Dynamic Semantic Publishing (DSP) of semantic entities or to publish your content into the cloud.
*  Postfix or Sendmail (if you want to use notification by mail see 'conf/mail.conf')

##Manual Installation Steps
If you prefer to control all the steps this is your installation method:

1. Download Ximdex package, tar file and expand it:
	```
  	wget --no-check-certificate https://github.com/XIMDEX/ximdex/archive/develop.zip
	unzip develop.zip
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
  	mysql myximdexDB -u root -p -h localhost < /var/www/myximdex/install/ximdex_data/ximdex.sql
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
	'db' => array(
        	'type' => 'mysql',
        	'host' => 'localhost',
        	'port' => '3306',
        	'user' => 'XIMDEX_DBUSER',
        	'password' => 'XIMDEX_DBPASS',
        	'db' => 'myximdexDB',
        	'log' => false
    	),
    	'ximdex_root_path' => '/var/www/myximdex',
    	'default.db' => 'db' ,
    	'timezone' => 'Europe/Madrid',
    	'locale' => 'en_US',
	...
	```
	Please, pay attention to the values in this file and the values you have chosen for the target directory (i.e.: /var/www/myximdex) and the parameters stored in the DB!!
	
10. Set File Owners and Permissions:
	```
	cd /var/www
	chown -R www-data:www-data myximdex
	chmod g+s myximdex/data
	chmod g+s myximdex/logs
	```
	
	
11. Open Ximdex with your browser to end the configuration (http://HOST/myximdex)

