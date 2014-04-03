#Installing the Semantic Web CMS Ximdex (Manual process)

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

1. Move yourself to the directory where Ximdex will reside (i.e.: /var/www/)
2. Untar the TGZ file
  ```
  tar zxvf ximdex.tgz .
  ```
2. Move it to your Web Server Document Root
  ```
  mv ximdexinstance /var/www/myximdex
  ```
2. Create the DB user
  ```
  GRANT ALL PRIVILEGES  ON myximdex.* TO 'myximdex'@'localhost' IDENTIFIED BY 'XIMDEX_DBPASS' WITH GRANT OPTION; 
  GRANT ALL PRIVILEGES  ON myximdex.* TO 'myximdex'@'%' IDENTIFIED BY 'XIMDEX_DBPASS' WITH GRANT OPTION; 

  ```
3. Import Ximdex DB to your DB server
  ```
  mysql ximdexDB -u root -pROOTPASSWD -h localhost --port 3306 /var/www/myximdex/install/ximdex_data/ximdex.sql
  UPDATE Config SET ConfigValue='http://MYHOST/myximdex' WHERE ConfigKEY='UrlRoot';
  UPDATE Config SET ConfigValue='/var/www/myximdex' WHERE ConfigKEY='AppRoot';
  UPDATE Users SET Login='ximdex' where IdUser = '301' 
  UPDATE Nodes SET Name='ximdex' where IdNode = '301'
  UPDATE Users SET Pass=MD5('XIMDEX_ADMIN_PASS') where IdUser = '301' 
  UPDATE Config SET ConfigValue='en_US' WHERE ConfigKEY='locale';
  UPDATE Config SET ConfigValue='myximdex35' WHERE ConfigKEY='ximid'; 
  ```
4. Update the parameters file for Ximdex:
5.
6.


