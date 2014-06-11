#Installing the Semantic Web CMS Ximdex

Ximdex CMS basically requires a Linux machine, a Database server as MySQL (or MariaDB) and Apache Web Server with PHP.

The easiest way to install Ximdex is by downloading it, setting file permissions and pointing your web browser to your Ximdex CMS to end its configuration. Additional installation methods as a fully manual or a fully automatic are described at install/ximdex_installation.md

###Requirements
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
Once PHP is running with the requested packages you have to download Ximdex CMS, move it to the final destination (i.e.: /var/www/myximdex), set permissions and file owners (user/group) in harmony with your web server configuration and configure it using your web browser pointing to its URL (i.e.: http://yourhost/myximdex). You will need root access to a unix console to execute some steps...

## Recommended Installation Steps

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

4. Point your web browser to your just installed Ximdex CMS instance (i.e.: http://YOURHOST/myximdex or http://localhost/myximdex) to load its database and configure it.

5. Thank you for installing Ximdex CMS. Please, contact us at help@ximdex.org if you need further assistance.

##Assisted Installation Steps
In case of neeeding any assistance to install Ximdex CMS we have prepared the XIMDEX_INSTALL.sh script that will download Ximdex for you, ask you some parameters (instance name, installation paths, etc.) and create a new script called 1.-MoveXimdexToDocRoot.sh that will move the directory to its final destination and set the right permissions. 

The file install/ximdex_installation.md provides the steps to execute.

