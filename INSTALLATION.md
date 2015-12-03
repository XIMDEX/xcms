#Installing the Semantic Web CMS Ximdex

Ximdex CMS requires a Linux host, a Database server (MySQL or MariaDB) and Apache Web Server with PHP.

You can install Ximdex CMS with Docker or using the web installer.
Additionally, a fully manual installation can be found at install/XIMDEX_manual_installation_guidelines.md.


## A) Installing Ximdex CMS using Docker

1. From a terminal run the command:

```
	docker run -d -h "host.domain" -e PORT=9090 -e XIMDEX_PASS=MyPass -p 9090:5000 ximdex/ximdex
```

> That will install Ximdex CMS running on host.domain and port 9090 with password MyPass for the admin user.

2. From your browser visit http://host.domain:9090 to end the installation.

3. Play with Ximdex CMS at http://host.domain:9090 using user Ximdex with password MyPass to login in.

> A shorter version of the docker command using default values is "docker run -d -p 5000:5000 ximdex/ximdex" that will end with ximdex running at http://localhost:5000 using ximdex as password for the ximdex user.


## B) Installing from Github with the Web Installer
When Apache2 and PHP are running with the requested packages you have to download Ximdex CMS, move it to the final destination on your document root (i.e.: /var/www/myximdex), set directory permissions and file owners (user/group) in harmony with your web server configuration and configure it using your web browser pointing to the desired URL (i.e.: http://yourhost/myximdex). You will need root access to a unix console to execute some steps...


### Requirements
*  A terminal with Telnet or SSH.
*  A user with enough permissions to create the directory where Ximdex CMS will be installed (under a document root for your web server)
*  Enough free space in the filesystem. See 'conf/diskspace.conf' file for further information.
*  A database as MySQL Server (>= 5.1) or MariaDB (>=5.5) and a DB user that can create the Ximdex Database.
*  Apache2 web server with modules libapache2-mod-php5, apache-mpm-worker (recommended).
*  PHP (>= 5.2.5).
	*  and PHP modules: php5-xsl, php5-cli, php5-curl, php5-gd, php5-mysql, php-pear, php5-suhosin (recommended).
	*  To use the spelling checker in Xedit (our wysiwyg XML editor): php5-enchant module.
*  Other packages: wget.
*  A modern web browser (with javascript and cookies enabled): Firefox, Google Chrome, Safari, Opera, Microsoft Edge, …
*  An internet connection if you want to use some features as the automatic suggestion system based on ontologies (XOWL module), Dynamic Semantic Publishing (DSP) of semantic entities or to publish your content into the cloud.
*  Postfix or Sendmail (if you want to use notification by mail see 'conf/mail.conf')

### Steps
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

4. **Point your web browser** to your just installed Ximdex CMS instance URL (i.e.: http://YOURHOST/myximdex or http://localhost/myximdex) to run the configuration tool that will load the database, create users and install Ximdex's modules.

## C) Assisted Installation Steps
If the previous method did not work or just in case of neeeding any assistance to install Ximdex CMS we have prepared the XIMDEX_INSTALL.sh script that will download Ximdex for you, ask you some parameters (instance name, installation paths, etc.) and create a new script called 1.-MoveXimdexToDocRoot.sh that will move the directory to its final destination and set the right permissions.
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

## E) Manual Installation Steps
If you prefer to control all the steps, please check the manual installation guide at install/XIMDEX_manual_installation_guidelines.md.


And thank you for installing Ximdex CMS. Please, contact us at help@ximdex.org if you need further assistance.
