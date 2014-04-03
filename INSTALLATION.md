#Installing the Semantic Web CMS Ximdex

Ximdex CMS basically requires a Linux machine, a Database server as MySQL (or MariaDB) and Apache Web Server with PHP.

The easiest way to install Ximdex is downloading the bash script `XIMDEX_INSTALL.sh` into a clean directory and run the script. Ximdex can also be found as TAR (or TGZ or zip) files, linux packages, virtual machines. The XIMDEX_INSTALL.sh script, additional flavours or old versions of Ximdex can be downloaded from http://www.ximdex.com

Ximdex can be installed using the following methods:

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
	*  Apache modules: libapache2-mod-php5, apache-mpm-worker(recommended).
*  PHP (>= 5.2.5).
	*  and PHP modules: php5-xsl, php5-cli, php5-curl, php5-gd, php5-mysql, php-pear, php5-suhosin (recommended).
	*  To allow spelling check using Xedit (our wysiwyg XML editor): php5-enchant module.
*  Other packages: wget.
*  For the client side (with javascript and cookies enabled): Firefox (>=3.6), Google Chrome, Safari or Opera, ...
*  An internet connection (if you want to use the automatic suggestion system based on ontologies and annotations provided by XOWL module or for dynamic remote publishing your content in the cloud).
*  Postfix or Sendmail (if you want to use notification by mail as it's defined by default). Otherwise, configure your 'conf/mail.conf'

See http://www.ximdex.org/documentacion/requirements_en.html for further information.


##Assisted Installation Steps

1. Make a directory where Ximdex will download, move there and **download the XIMDEX_INSTALL.sh** script:
	```shell
	mkdir tryximdex
	cd tryximdex
	wget wget --no-check-certificate https://raw.githubusercontent.com/XIMDEX/ximdex/develop/XIMDEX_INSTALL.sh
	```

2. **Prepare the answers** to the questions the installation script will ask you:
	- If you want to modify the name for your Ximdex instance (i.e.: myximdex)
	- Target `directory` to install Ximdex (i.e.: /var/www). 
		- Your web server has to consider it a `DOCROOT` (document root for the web server with PHP capabilities). Please, be sure it is a suitable directory to run PHP code.
		- Ximdex files will be finally stored there (i.e.: /var/www/myximdex)
	- The `URL` where Ximdex will be accessed (i.e.: http://YOURHOST/myximdex)
	- User Name and Group for your Apache Web Documents to set file owners.
	- Access credentials to your Database Server (i.e.: MySQL) as its HOSTNAME and PORT, the user with privileged access to create the ximdex database and its password. The script will then create the Ximdex DB for you (i.e.: myximdex database).
	- Some parameters for Ximdex:
		* Username (i.e.: myximdex) to access the just created Ximdex database (please, do not use the privileged database user. The root user is only required to create the DB but not to daily run Ximdex CMS)
		* Default Language for your Ximdex (English, German, Portuguese or Spanish)
		* Password for the Ximdex user with CMS ADMIN privileges (by the way, it is named `ximdex`)


3. **Run** the Installation script with:
	```
	bash XIMDEX_INSTALL.sh
	```

4. The last step of the installation will automatically create the File `1.MoveXimdexToDocroot` that has to be run as ROOT. This script will copy your instance to its final directory (i.e.: /var/www/myximdex), set file owners (to the user running apache, i.e.: www-data) and set permissions:
	4. The installer will ask you to run this script via `sudo` (with superuser privileges asking for your password and if you have no sudo access will try a `su` command asking for root password). You can find the generated script and read it at the `install` directory.
	4. If you decline to run it with superuser privileges you will have to run the steps in the generated script as root directly to move your instance to the final directory and set adequate file owners and permissions.

5. Finally you will be asked to **visit the Ximdex URL** from your browser (http://YOURHOST/myximdex). This last step will test your instance, create templates for new projects, allow you to install additional modules and finally will clean the install directory.

That's all folks. Enjoy Ximdex!

And contact us at help@ximdex.org if you need further assistance or want to make any comment or suggestion.


