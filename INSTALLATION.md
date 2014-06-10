#Installing the Semantic Web CMS Ximdex

Ximdex CMS basically requires a Linux machine, a Database server as MySQL (or MariaDB) and Apache Web Server with PHP.

The easiest way to install Ximdex is by downloading it, setting permissions and pointing your web browser to your Ximdex CMS to end its configuration. Additional installation methods as a fully manual or automatic methods are described at install/manual_install.md

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

## Recommended Installation Steps
For this installation you have to prepare your downloaded Ximdex CMS instance in its final destination (i.e.: /var/www/myximdex) and set permissions and user/group for your web server configuration (you will need root access and a unix console to do it). End the installation visiting your just prepared Ximdex CMS (i.e.: http://yourhost/myximdex) from your browser to load the database and configure Ximdex.


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

3. **Set File Owners and Permissions ** (user/group are those used by your web server):
	```
	cd /var/www
	chown -R www-data:www-data myximdex
	cd myximdex
	chmod g+s data
	chmod g+s logs
	```

	You may need superuser privileges to do that!

	> So, in this example, user and group 'www-data' will be running the web processes linked to Ximdex CMS .

4. Point your web browser to your just installed Ximdex instance (i.e.: http://YOURHOST/myximdex) to end its configuration.
5. That's all folks. Enjoy Ximdex! Contact us at help@ximdex.org if you need further assistance.


##Assisted Installation Steps

In case of neeeding any assistance to install Ximdex CMS we have prepared the XIMDEX_INSTALL.sh script that will download Ximdex for you, ask you for the name of the instance, installation paths, etc., and it will automatically make a script called 1.-MoveXimdexToDocRoot.sh that will move the directory to its final destination and set the right permissions.

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




