


# Installing XCMS v.4

### Server Requirements

To use **XCMS** you need an updated web browser as Firefox, Google Chrome, ... with Javascript and cookies enabled.

To install **XCMS** you need a Linux server with:

#### 1. A Relational Database Management System as

##### MySQL

* Recommended version: **5.7** (or greater)
* Linux install: `sudo apt-get install mysql-server`

##### or MariaDB

* Recommended version: **10.2** (or greater)
* Alternative versions: prior versions, like **10.1**, reported some errors.
* Linux install: `sudo apt-get install mariadb-server`

#### 2. A web server as Apache with PHP 7.2 or greater

  * Install Apache web server and PHP:   
     ```shell
     sudo apt-get install apache2
     sudo apt-get install php
     ```
  
  * PHP Modules:
     ```shell
     sudo apt-get install php-xml
     sudo apt-get install php-cli
     sudo apt-get install php-curl
     sudo apt-get install php-gd
     sudo apt-get install php-mysql
     sudo apt-get install php-pear
     sudo apt-get install php-mbstring
     sudo apt-get install php-enchant
     ```

#### Email notifications

To use email notifications **Postfix** or **Sendmail** are needed. You can install postfix with `sudo apt-get install postfix` and **Sendmail** with `sudo apt-get install sendmail`.

#### Install PHP Composer

To install composer please visit this [link](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos) and follow the instructions.

---

## XCMS v4 installation

To install XCMS:

##### 1. Download XCMS

Download it from github (develop branch) at https://github.com/XIMDEX/ximdex/tree/develop or use **curl** or **wget**:

   ```shell
   wget --no-check-certificate https://github.com/XIMDEX/ximdex/archive/develop.zip
   ```

   ```shell
   curl -L https://github.com/XIMDEX/ximdex/archive/develop.zip > develop.zip
   ```

(Install wget with ```sudo apt-get install wget```)

Unpack the package (Manually or using unzip):

   ```shell
   unzip develop.zip
   ```

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/Selección_020.png)

##### 2. Move it to the server root

You need to move the **ximdex-develop** folder to the server documents root. You can use `mv ximdex-develop /YOUR/ROOT/ADDRESS/myximdex` to move it and rename the instance from 'ximdex-develop' to 'myximdex'.

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/Selección_021.png)

In this example, our root is located at **www** and our instance is renamed **myximdex**.

##### 3. Set File Owners and Permissions

We need to set file owners and permissions adequated to our web server. So, if apache runs as 'www-data:www-data' we can run:

   ```shell
   cd /var/www/
   sudo chown -R www-data:www-data myximdex
   cd myximdex
   sudo chmod -R ug+rw data
   sudo chmod -R ug+rw logs
   sudo chmod -R ug+rw conf
   ```

Optionally, if the owner is not the apache unix user, you have to set the sticky bit to assign the right group owner to new files:

   ```shell
   sudo chmod -R g+s data
   sudo chmod g+s logs
   sudo chmod g+s conf
   ```

##### 4. Install third-party needed repositories with **composer**

Move to the XCMS root folder (**myximdex** in this case) and run **composer** to configure additional packages:

   ```shell
   cd /var/www/myximdex
   composer install --no-dev
   ```

##### 5. Create your XCMS Database

Open a connection to your DDBB engine and type the following SQL commands:

* To create the DB for XCMS:

   ```sql
   CREATE DATABASE `ximdex_db`;
   ```

* Create a specific db user for XCMS:

   ```sql
   CREATE USER 'ximdex-user'@'localhost' IDENTIFIED BY 'ximdex-pass';
   GRANT ALL PRIVILEGES ON 'ximdex_db'.* TO 'ximdex-user'@'localhost' WITH GRANT OPTION;
   ```



---

## Configure Ximdex CMS v4

Once XCMS is installed at the Web Server, point your browser to <http://YOURHOST/myximdex> (In this case <http://localhost/myximdex>) and follow the suggested steps to load the DataBase, create the XCMS admin user and install additional XCMS modules.

The landing page will greet us with a button to check if all requirements have been satisfied:

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/023.png)

Once clicked, if all the requirements are fullfiled, the browser will show a success notification:

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/Selección_024.png)

The "start installation" button will launch the DB configuration screen where we will be prompted for a user and pass for the database (we must provide the previously created ones)

Press the **create database** button (select "yes" if it shows a overwrite warning)

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/026.png)

In the following step, an **unprivileged user** to access the DataBase from XCMS can be created. It is highly recommended to create it and do not skip this step. In this case, we will create one called **ximdex_user** with password **ximdex_user**:

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/Selección_027.png)

In the following screen, we assign the password for **XCMS superuser** (the priviledged user is called "ximdex").

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/028.png)

Next, additional components (as XML editors, publishing reports, semantic tag management, ...) will be installed when pressing the **install modules** button:

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/029.png)

The last screen configures the semantic service to enrich your content and data automatically. If you click **continue**, a  default key will be in use. Visit my.ximdex.net to generate your private key.

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/031.png)

---

### Run Automatically the Transforming and Publishing System

Remember that XCMS is an omnichannel headless CMS that transform and publish your documents in remote locations. To do it, add the following crontab job to your root user:
   ```
   * * * * * php /var/www/html/myximdex/bootstrap.php src/Sync/scripts/scheduler/scheduler.php
   ```

---
Thank you for installing **Ximdex CMS**. Please, contact us at **help@ximdex.org** if you need further assistance.
