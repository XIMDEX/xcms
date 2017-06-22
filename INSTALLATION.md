# Installing the Semantic Web CMS Ximdex

Ximdex CMS requires a Linux host, a Database server (MySQL or MariaDB) and Apache Web Server with PHP or NGINX with PHP-fpm.

You can install Ximdex CMS with Docker or using the web installer.

> Additionally, a fully manual or automated installation method can be found at install/XIMDEX_manual_installation_guidelines.md.

## A) Running Ximdex CMS using Docker composer

1. **Download Ximdex** package (https://github.com/XIMDEX/ximdex/archive/develop.zip) and expand it:
	```
  	wget --no-check-certificate https://github.com/XIMDEX/ximdex/archive/develop.zip
	unzip develop.zip && rm develop.zip
  	```
	or
	```
  	curl -L https://github.com/XIMDEX/ximdex/archive/develop.zip > develop.zip
	unzip develop.zip && rm develop.zip
  	```
  	
	> You should end with a directory (i.e.: ximdex-develop) containing all the Ximdex files and directories. 

	If you **don´t have installed the docker package**, install it using the next command line in a terminal console:
	
	```
	sudo apt-get install docker-compose
    ```
    
2. Open a terminal under the directory ximdex-develop, which has been unzipped, and run the command (launch it into the root of this repository, where the file docker-compose.yml is located):

    ```
	sudo docker-compose up
    ```
    > That will run the containers for Apache2, PHP, MySQL and Ximdex running on localhost:80 (the directory with ximdex has to be in a shared path for docker)

3. From your Chrome, Firefox, Safari or different browser visit http://localhost to end the installation.

    If you get some errors like these ones in the step one:
    
    > Check permission on directory /conf/, Read and Write are required to install Ximdex
    Check permission on directory /data/, Read and Write are required to install Ximdex
    ...
    
    You need to grant read and write permissions to these directories, which have been placed in our Ximdex installation directory, by this way:

    ```
    sudo chmod -R 777 data
    sudo chmod 777 logs
    sudo chmod 777 conf
    ```
    
    > For docker, you will need to use the host **db** instead of the suggested **localhost** and the password **ximdex** in the "*Database password*" field, to make Ximdex installation able to access to the database server, and create the data schema.

    If the **installation is aborted**, please use the next conmmand to remove the .data directory at ximdex to clean the database data:
    ```
    sudo rm -rf .data
    ```

4. Play with Ximdex CMS at http://localhost using user Ximdex with the choosen password.

To stop the services, run
```
sudo docker-compose down
```
from the root directory where the composer was launched.

## B) Installing from Github with the Web Installer
When Apache2 and PHP are running with the requested packages you have to download Ximdex CMS, move it to the final destination on your document root (i.e.: /var/www/myximdex, in some cases this may be /var/www/html/), set directory permissions and file owners (user/group) in harmony with your web server configuration and configure it using your web browser pointing to the desired URL (i.e.: http://yourhost/myximdex). You will need root access to a unix console to execute some steps...

### Requirements
*  A terminal with Telnet or SSH.
*  A user with enough permissions to create the directory where Ximdex CMS will be installed (under a document root for your web server)
*  Enough free space in the filesystem. See 'conf/diskspace.php' file for further information.
*  A **database server** like *MySQL Server* (>= 5.6) or *MariaDB* (>=5.5) and a database user that can create the Ximdex Database.
    i.e. you can execute in a terminal console for MySQL server:
    ```
    sudo apt-get install mysql-server
    ```
    or for MariaDB server:
    ```
    sudo apt-get install mariadb-server
    ```
    If you need to install one of them.
    
*  **Apache2 web server** with modules libapache2-mod-php, apache-mpm-itk (recommended).
    ```
    sudo apt-get install apache2
    sudo apt-get install libapache2-mod-php
    sudo apt-get install libapache2-mpm-itk
    ```
    
*  **PHP** (>= 5.6.0)
    * PHP package:
        ```
        sudo apt-get install php
        ```
	* PHP modules: php-fxsl, php-cli, php-curl, php-gd, php-mysql, php-pear:
        ```
        sudo apt-get install php-fxsl
        sudo apt-get install php-cli
        sudo apt-get install php-curl
        sudo apt-get install php-gd
        sudo apt-get install php-mysql
        sudo apt-get install php-mcrypt
        sudo apt-get install php-pear
        ```
	*  To use the spelling checker in Xedit (our wysiwyg XML editor), install php-enchant module:
        ```
	    sudo apt-get install php-enchant
	    ```
    *  Other packages: wget
        ```
        sudo apt-get install wget
        ```
        
*  A **modern web browser** (with javascript and cookies enabled): Firefox, Google Chrome, Safari, Opera, Microsoft Edge, etc.

*  An **internet connection** if you want to use some features as the automatic suggestion system based on ontologies (XOWL module), Dynamic Semantic Publishing (DSP) of semantic entities or to publish your content into the cloud.

*  *Postfix* or *Sendmail* (if you want to use notification by mail see 'conf/mail.php').
    So, for Postfix use:
    ```
    sudo apt-get install postfix
    ```
    or for Sendmail use:
    ```
    sudo apt-get install sendmail
    ```

### Steps
1. **Download Ximdex** package (https://github.com/XIMDEX/ximdex/archive/develop.zip) and expand it:
	```
  	wget --no-check-certificate https://github.com/XIMDEX/ximdex/archive/develop.zip
	unzip develop.zip
  	```
	or
	```
  	curl -L https://github.com/XIMDEX/ximdex/archive/develop.zip > develop.zip
	unzip develop.zip
  	```
	> You should end with a directory (i.e.: ximdex-develop) containing all the Ximdex files and directories.

2. **Move it to your Web Server Document Root** with the name you want (i.e.: myximdex)
	```
	mv ximdex-develop /var/www/myximdex
	```
	_Remember that this is the location where your **Apache document root** is there. Probably you may have a different one in case you have changed it in Apache configuration. It's possible that this location was placed in */var/www/html* directory by default in some systems._

    You may **need superuser privileges** to do that! In that case type sudo before the command (i.e.: sudo mv ...)
	> So, in this example, 'myximdex' will be your Ximdex instance after installing it.

3. **Set File Owners and Permissions** (file owners should be those in use in your web server):
	```
	cd /var/www/
	```
	_Remember that this is the location where your **Apache document root** is there. Probably you may have a different one in case you have changed it in Apache configuration. It's possible that this location was placed in */var/www/html* directory by default in some systems._
	
	```
	chown -R www-data:www-data myximdex
	cd myximdex
	chmod -R g+s data
	chmod g+s logs
	chmod g+s conf
	```
	You may **need superuser privileges** to do that! (Type *sudo* before the above commands)

	> So, in this example, user and group 'www-data' are running the web processes as declared in the apache configuration file.

4. In your database administrator (like _MySQL Workbench_ or _PHPMyAdmin_) you must **create a new database schema** which name will be used when the installation process begin.
Here we provide the SQL code to make it in SQL command way (use de database and user names as you prefer):
    ```
    CREATE DATABASE `ximdex-db`;
    ```
    Now we need an user to accesss this schema, with all privileges. If you have to create a new one, we can help you with this SQL statements:
    ```
    CREATE USER `ximdex-user`@`localhost` IDENTIFIED WITH mysql_native_password AS `ximdex-pass`;
    ```
    Finally we will make access for the new database created to this new user:
    ```
    GRANT ALL PRIVILEGES ON `ximdex-db`.* TO `ximdex-user`@`localhost` WITH GRANT OPTION;
    ```
    > Remember to use this information to generate de database schema in the point 5.

5. **Point your web browser** to your just installed Ximdex CMS instance URL (i.e.: http://YOURHOST/myximdex or http://localhost/myximdex) to run the configuration tool that will load the database, create users and install Ximdex's modules.

## C) Manual and Automated Installation methods
If the previous methods did not work, want to control all the steps or to automate the installation process, visit:

> install/XIMDEX_manual_installation_guidelines.md file.


**Thank you for installing Ximdex CMS**. Please, contact us at help@ximdex.org if you need further assistance.
