# Installing the Semantic Web headless CMS Ximdex

Ximdex CMS requires a Linux host, a Database server (MySQL or MariaDB) and the Apache Web Server with PHP or NGINX with PHP-fpm.

Install Ximdex CMS as a docker container or using the web installer on your server.

> A fully manual or automated installation method can be found at install/XIMDEX_manual_installation_guidelines.md.

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
  	
	You should end with a directory (i.e.: ximdex-develop) containing all the Ximdex files and directories.


2. From the Ximdex directory (i.e.: ximdex-develop, where the docker-compose.yml file is locate) run the command:
    ```
	sudo docker-compose up
    ```
    That will run the containers for Apache2, PHP, MySQL and Ximdex running on the host ximdex:80 
	
3. Add to your /etc/hosts file the line:
	```
	127.0.0.1		ximdex
	```    

4. Visit http://ximdex to start the web installer:
    
    > For docker, you will need to use the host **db** instead of the suggested **localhost** and the password **ximdex** in the **"Database password"** field.

5. Use Ximdex CMS at http://ximdex with the user Ximdex and your choosen password.

6. To **stop the services**, run
	```
	sudo docker-compose down
	```
	from the root directory where the composer was launched.


### Docker problems and solutions:

The ximdex directory has to be a shared path for docker!
	    
If you **don´t have installed the docker-composer package**, install it using the next command line in a terminal console:
	    
```
sudo apt-get install docker-compose
```
	
If the **installation is aborted**, please use the next conmmand to remove the .data directory at ximdex to clean the database data:
```
sudo rm -rf .data
```
	
You may need to grant read and write permissions to web server user and group:
	
```
sudo chown -R www-data:www-data ximdex-develop
cd ximdex-develop
sudo chmod -R ug+rw data
sudo chmod -R ug+rw logs
sudo chmod -R ug+rw conf

sudo chmod -R g+s data (optional)
sudo chmod g+s logs (optional)
sudo chmod g+s conf (optional)
```
	
## B) Installing from Github with the Web Installer
When Apache2 and PHP are running with the requested packages, download Ximdex CMS, move it to the final destination on your document root (i.e.: /var/www/myximdex, in some cases this may be /var/www/html/), set directory permissions and file owners (user/group) and configure it using your web browser pointing to the desired URL (i.e.: http://yourhost/myximdex). You will need root access to a unix console to execute some steps...

### Requirements
*  A terminal with Telnet or SSH.
*  A user with the power to create the directory where Ximdex CMS will be installed (under a document root for your web server)
*  Enough free space in the filesystem. See 'conf/diskspace.php' file for further information.
*  A **database server** like *MySQL Server* (>= 5.7) or *MariaDB* (>= 10.2) and a database user that can create the Ximdex Database.
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
    
*  **PHP** (>= 7.1.0)
    * PHP package:
        ```
        sudo apt-get install php
        ```
	* PHP modules: php-xml, php-cli, php-curl, php-gd, php-mysql, php-mcrypt, php-pear:
        ```
        sudo apt-get install php-xml
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

### Installation Steps
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

2. **Move it to your Web Server Document Root** with the service name to use (i.e.: myximdex)
	```
	mv ximdex-develop /var/www/myximdex
	```
	In this example, 'myximdex' will be your Ximdex instance after installing it.
    	> You may **need superuser privileges** to do that! In that case type sudo before the command (i.e.: sudo mv ...)

3. **Set File Owners and Permissions** to the required by your web server: 
	```
	cd /var/www/
	chown -R www-data:www-data myximdex
	cd myximdex
    	sudo chmod -R ug+rw data
    	sudo chmod -R ug+rw logs
    	sudo chmod -R ug+rw conf
    
    	sudo chmod -R g+s data (optional)
    	sudo chmod g+s logs (optional)
    	sudo chmod g+s conf (optional)
	```
	In this example, 'www-data' are the user and group that apache runs on.
	> You may **need superuser privileges** to do that! (Type *sudo* before the above commands)


4. **Create a new database and a new user**:
    ```
    CREATE DATABASE `ximdex-db`;
    CREATE USER 'ximdex-user'@'localhost' IDENTIFIED BY 'ximdex-pass';
    GRANT ALL PRIVILEGES ON `ximdex-db`.* TO 'ximdex-user'@'localhost' WITH GRANT OPTION;
    ```
    > This information will be requested in the following step.

5. **Point your web browser** to your just installed Ximdex CMS instance URL (i.e.: http://YOURHOST/myximdex or http://localhost/myximdex) to run the configuration tool that will load the database, create users and install Ximdex's modules.


## C) Manual Installation

If you want to control all the installation process, visit install/XIMDEX_manualinstallation_guidelines.md.


**Thank you for installing Ximdex CMS**. Please, contact us at help@ximdex.org if you need further assistance.


