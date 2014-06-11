#Installing the Semantic Web CMS Ximdex (automatic, manual and assisted)

Ximdex CMS basically requires a Linux machine, a Database server as MySQL (or MariaDB) and Apache Web Server with PHP.

The easiest way to install Ximdex is by downloading it, setting file permissions and pointing your web browser to your Ximdex CMS to end its configuration. 

The easiest way to install Ximdex is downloading the bash script `XIMDEX_INSTALL.sh` into a clean directory and run the script. Ximdex can also be found as TAR (or TGZ or zip) files, linux packages, virtual machines. The XIMDEX_INSTALL.sh script, additional flavours or old versions of Ximdex can be downloaded from http://www.ximdex.com

Install Ximdex using one of the following methods:
- **Recommended**: the easiest way to install Ximdex is by downloading it, setting file permissions and pointing your web browser to your Ximdex CMS to end its configuration. This method is described at the INSTALLATION.md file.

- **Assisted**: the `XIMDEX_INSTALL.sh` script will ask you for the the name of the instance, installation pahts, etc.; and it will create a script called `1.-MoveXimdexToDocRoot.sh` that will run with root privileges (via sudo) or, if you want to control which commands are executed, will suggests you to run the commands from a root console.
	- **Automatic**: the XIMDEX_INSTALL.sh script with '-a setupfile' option will make all steps automatically. A commented template setup is at install/templates/setup.conf.
	
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

See http://www.ximdex.org/documentacion/requirements_en.html for further information.

## Recommended Installation Steps
Once PHP is running with the requested packages you have to download Ximdex CMS, move it to the final destination (i.e.: /var/www/myximdex), set permissions and file owners (user/group) in harmony with your web server configuration and configure it using your web browser pointing to its URL (i.e.: http://yourhost/myximdex). You will need root access to a unix console to execute some steps...

1. **Download Ximdex** package (https://github.com/XIMDEX/ximdex/archive/develop.zip) and expand it:
	```
  	wget --no-check-certificate https://github.com/XIMDEX/ximdex/archive/develop.zip
	unzip develop.zip
  	```
	> You should end with a directory (i.e.: ximdex-develop) containing all the Ximdex files and directories.

2. **Move it to your Web Server Document Root** with the name you want (i.e.: myximdex)

	```
	mv ximdex-develop /var/www/myximdex
	```
	You may need superuser privileges to do that! In that case type sudo before the command (i.e.: sudo mv ...)

	> So, in this example, 'myximdex' will be your Ximdex instance after installing it.

3. **Set File Owners and Permissions** (file owners should be those in use in your web server):
	```
	cd /var/www
	chown -R www-data:www-data myximdex
	cd myximdex
	chmod g+s data
	chmod g+s logs
	```

	You may need superuser privileges to do that! (Type sudo before the commands)

	> So, in this example, user and group 'www-data' are running the web processes as declared in the apache configuration file.

4. Point your web browser to your just installed Ximdex CMS instance URL (i.e.: http://YOURHOST/myximdex or http://localhost/myximdex) to run the configuration tool that will load the database, create users and install Ximdex's modules.


5. Thank you for installing Ximdex CMS. Please, contact us at help@ximdex.org if you need further assistance.


##Assisted Installation Steps
In case of neeeding any assistance to install Ximdex CMS we have prepared the XIMDEX_INSTALL.sh script that will download Ximdex for you, ask you some parameters (instance name, installation paths, etc.) and create a new script called 1.-MoveXimdexToDocRoot.sh that will move the directory to its final destination and set the right permissions. 
>This installation can be fully automated or interactive, it is less prone to errors and let you decide how to run commands requiring a superuser. The steps are:


1. Make a directory where Ximdex will download, move there and **download the XIMDEX_INSTALL.sh** script:
	```shell
	mkdir tryximdex
	cd tryximdex
	wget --no-check-certificate https://raw.githubusercontent.com/XIMDEX/ximdex/develop/XIMDEX_INSTALL.sh
	```

2. **Prepare the answers** to the questions the installation script will ask you:
	- If you want to modify the name for your Ximdex instance (i.e.: myximdex)
	- Target `directory` to install Ximdex (i.e.: /var/www). 
		- Your web server has to consider it a `DOCROOT` (document root for the web server with PHP capabilities). Please, be sure it is a suitable directory to run PHP code.
		- Ximdex files will be finally stored there (i.e.: /var/www/myximdex) and the URL where Ximdex will be accessed (i.e.: http://YOURHOST/myximdex) will be calculated
	- User Name and Group for your Apache Web Documents to set file owners.


3. **Run** the Installation script with:
	```
	bash XIMDEX_INSTALL.sh
	```

4. The last step of the installation will automatically create the File `1.MoveXimdexToDocroot` that has to be run as ROOT. This script will copy your instance to its final directory (i.e.: /var/www/myximdex), set file owners (to the user running apache, i.e.: www-data) and set permissions:
	4. The installer will ask you to run this script via `sudo` (with superuser privileges asking for your password and if you have no sudo access will try a `su` command asking for root password). You can find the generated script and read it at the `install` directory.
	4. If you decline to run it with superuser privileges you will have to run the steps in the generated script as root directly to move your instance to the final directory and set adequate file owners and permissions.

5. Finally you will be asked to **visit the Ximdex URL** from your browser (http://YOURHOST/myximdex). This last step will test your instance, create templates for new projects, allow you to install additional modules and finally will clean the install directory.

6. That's all folks. Enjoy Ximdex! Contact us at help@ximdex.org if you need further assistance or want to make any comment or suggestion.


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

