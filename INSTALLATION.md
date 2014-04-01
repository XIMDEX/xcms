Semantic Web CMS Ximdex - INSTALLATION
--------------------------------------

Installing Ximdex CMS basically requires a Linux machine with MySQL or MariaDB, Apache Web Server with PHP.

Ximdex is distributed as TAR (or TGZ) files, debian or RPM packages, Virtual machines, etc., but the easiest way to install it is downloading the bash script XIMDEX_INSTALL.sh into a clean directory and running it.

This file provides information for a Manual Installation and the assisted installation (via the XIMDEX_INSTALL.sh shell script) both as interactive or as automatic.

Before starting the installation check if you comply with the requeriments. Then select the process you want to follow and move to that section:

* Manual: unzipping the Ximdex instance, creating the database, assigning permissions, creating database users, parameterizing Ximdex, etc.

* Assisted: the XIMDEX_INSTALL.sh script will ask you for final target directory, name for the instance, name of the Ximdex database, usernames and passwords, etc. The script will create a script named 1.-MoveXimdexToDocRoot.sh that have to be run as root or executed by you as root step by step. This way, you control exactly what commands are run as superuser.

* Automatic: the XIMDEX_INSTALL.sh script with '-a setupfile' option will make all steps automatically.
 
Feel free to contact us at help@ximdex.org for further assistance.


Installation Requirements and dependencies:
------------------------------------------
	
*  Access to a Console or terminal with Telnet o SSH for the installation.
*  Some steps need root access (basically to install the Ximdex instance into the Document Root of your web server and adjust permissions). The installer will run as a non privileged user and will ask you to "sudo" a script that is generated during installation. You can also run it directly later to end the installation.
*  A MySQL user with write permissions on Ximdex database schema.
*  MySQL Server (>= 5.1) or MariaDB .
*  PHP (>= 5.2.5).
*  PHP modules: php5-xsl, php5-cli, php5-curl, php5-gd, php5-mysql, php-pear, php5-suhosin(recommended).
*  To allow spelling check using Xedit (our wysiwyg XML editor): php5-enchant module.
*  Other packages: wget.
*  Apache2 web server.
*  Apache modules: libapache2-mod-php5, apache-mpm-worker(recommended).
*  Permissions on file system: where the instance will reside.
*  For the client side: Firefox web browser (>=3.6) with Javascript y cookies enabled. Google Chrome, Safari and Opera browsers can be used too.
*  An internet connection (if you want to use the automatic suggestion system based on ontologies and annotations provided by XOWL module or for dynamic remote publishing your content in the cloud).
*  A disk partition with enough free space for Ximdex. See 'conf/diskspace.conf' file for further information.
*  Postfix or Sendmail (if you want to use notification by mail as it's defined by default). Otherwise, configure your 'conf/mail.conf'

See http://www.ximdex.org/documentacion/requirements_en.html for further information and http://www.ximdex.org/descargas.html for additional flavours of Ximdex and old versions.


SECTION A - PREPARATION: preparing the instance to be installed:
---------------------------------------------------------------

  In order to prepar your Ximdex instance before installation, please choose one method from this section:


  Installing from a TGZ or TAR file (preferred method for uptodate versions):
  ---------------------------------

	If you downloaded a '.tgz' package:
    
   		1) Untar the package in your web server root folder (i.e: /var/www, /var/www/html, ...). We'll use '/var/www/'. Choose the right one for you:
			>$ cp <current_path>/ximdex-VERSION_open_rREVISION.tgz /var/www/
                        >$ cd /var/www/			
			>$ tar zxvf /var/www/ximdex-VERSION_open_rREVISION.tgz 

		  By default, your instance will be named as 'ximdex_VERSION'

		2) Continue the installation in SECTION B - INSTALLATION 



SECTION B - INSTALLATION to install and configure your Ximdex instance:
----------------------------------------------------------------------

  To start this section you should have a Ximdex instance installed in a directory under your web root. Debian Lenny users of our repository and OVA installations do not have to execute this step.


  Please, follow these steps to install and configure your Ximdex instance...

  1) Move to the location of Ximdex (<ximdex_home>).

  2) Run the install script of Ximdex as a privileged user (it is important to run it from the <ximdex_home>): 

		 >$ sudo bash ./install/install.sh


  3) Follow the installation instructions and fill the requested data:

	3.1) Set the MySQL server url. By default, 'localhost'.

		>$ Database server [localhost]:

	3.2) Set the MySQL Admin user name. By default, 'root'.

		>$ Admin database user [root]:

	3.3) Set the MySQL Admin user password. Password should be typed twice.

		>$ Admin database password:
		>$ Admin database password (repeat):

	3.4) Set the database name to load all the Ximdex data. By default, 'ximdex'. If provided database already exists, the system will ask if you want to overwrite it, use it, or provide a new one.

		>$ Database name [ximdex]:

	3.5) Set the database user. The database name provided in the last step will be taken as default value as database user.

		>$ Database user [<XIMDEX_DB_NAME>]:

	3.6) Set the database user password. Password should be typed twice. If the typed user is the Admin provided above, password will not be asked again.

		>$ Database user password:
		>$ Database user password (repeat):

	3.7) Insert the url to access Ximdex with the browser. Like: 'http://my_domain/<XIMDEX_CURRENT_FOLDER>'. By default, 'http://localhost/<XIMDEX_DB_NAME>' 
		
		>$ Ximdex host [http://localhost/<XIMDEX_DB_NAME>]:

		(it is important for Ximdex to work that you use this declared URL and do not change it to synonyms as IP numbers or other hostnames)

	3.8) Insert the Ximdex local path. By default, '/var/www/<XIMDEX_DB_NAME>'.

		>$ Ximdex path [/var/www/<XIMDEX_DB_NAME>]:

	3.9) Set Ximdex Admin user name. By default, 'ximdex'.

		>$ Ximdex admin user [ximdex]:

	3.10) Set Ximdex Admin user password. Password should be typed twice.

		>$ Ximdex admin password:
		>$ Ximdex admin password (repeat):

	3.11) Set your interface language by default. Ximdex will show you at this point a list of available language, and you should choose one. By default, 'Spanish (es-ES)'.
		>$ Select your Ximdex default language choosing betweeen:
		        1. English
		        2. Spanish
		        3. German
		        4. Portuguese

		Ximdex default lenguage[1]: 

	3.12) Decide if you want to help us to improve (optional).
		>$ Would you like to help us to improve sending information about Ximdex usage? (recommended)  [y/n]: 

	3.13) Install test projects on Ximdex (optional). We provide three projects to allow you to start working with Ximdex right away!

		>$ Do you want to install one of our demo project? [y/n]:
		Available projects:
			1. AddressBook
			2. Picasso
			3. The_Hobbit

	3.14) Optionally, decide if you want to install a set of recommended basic modules (ximIO, ximSYNC, ximNEWS,ximTAGS), in order to ensure the full functionality of Ximdex

		>$ Do you want to install our recommended modules? [y/n]:
	
	3.15) Optionally, publishing processes have to be added to your crontab (i.e.: to sync to remote servers and to generate news bulletins), 

		>$ Do you want to add Automatic and Scheduler to your crontab? [y/n]:
 
4) In any moment after installation, you can install additional Ximdex modules. They are avaliable on '<XIMDEX_HOME>/modules/' folder:

	>$ ./install/module.sh install <MODULE_NAME>


5) Now, go to the browser and type the Url provided in step 3.7 (default: http://localhost/<XIMDEX_DB_NAME>)
	

That's all folks. Enjoy Ximdex!

