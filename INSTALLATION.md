![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/ximdex.png)

---

# 					Ximdex CMS 4.0	

## 		 				Install and deployment guide

---

# Requirements

For a correct **Ximdex CMS** instalation and deploy there are some previous server level requirements and dependencies that must be met .

## Database Management System

At the moment Ximdex support two differents DBMSs, **MySQL** and **MariaDB**, with some additional restrictions detailed next.

### MySQL

* Recommended version: **5.7**.
* Alternative versions: Higher versions had not being tested, however, they should work with any issues, because of a existent retrocompatibility.
* Linux install: `sudo apt-get install mysql-server`

### MariaDB

* Recommended version: **10.2**.
* Alternative versions: Instance with prior versions, like **10.1**, have been reported some errors, however  like with MySQL, higher versions should work without any issue.
* Linux install: `sudo apt-get install mariadb-server`

### Other DBMS

Ximdex doesn't support any other DBMS at the moment.

## Other requirements

Additionally this CMS needs to satisfy the following dependencies for a succesful deployment.

### PHP

* Recommended version: **7.2**.

* Alternative versions: Alternatively version **7.1** can be used, but not lower, because of some PHP dependencies that needs that version or higher.

* Linux install:

  1. Main package: `sudo apt-get install php`

  2. Modules:
     ```shell
     sudo apt-get install php-xml
     sudo apt-get install php-cli
     sudo apt-get install php-curl
     sudo apt-get install php-gd
     sudo apt-get install php-mysql
     sudo apt-get install php-pear
     sudo apt-get install php-mbstring
     ```

  3. Grammar corrector for XEdit **(Optional)**: `sudo apt-get install php-enchant`

  4. Other packages **(Optional)**: `sudo apt-get install wget`

### Email

To enjoy the Ximdex email notification feature **Postfix** is needed. You can install it using `sudo apt-get install postfix`. 

Additionally you can use **Sendmail** instead installing it with `sudo apt-get install sendmail`.

### A modern web browser

Firefox, Google Chrome, Safari, Opera, Microsoft Edge...etc with Javascript and cookies enabled.

### Composer

To install composer please visit this [link](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos) and follow the instructions.

# Instance installation

Once we have met the previous requirements needed by the CMS we can proceed to the installation in our chosen machine.

## 1. Package download

Before installation we need to download the Ximdex 4.0 Github package, currently available in the **develop** branch. For this end we can use the github interface at https://github.com/XIMDEX/ximdex/tree/develop or use **wget**:

```shell
wget --no-check-certificate https://github.com/XIMDEX/ximdex/archive/develop.zip
```

Or **curl**:

```shell
curl -L https://github.com/XIMDEX/ximdex/archive/develop.zip > develop.zip
```

Then we unpack the package (Manually or using unzip):

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/Selección_020.png)

## 2. Moving to the server root

Once we have extracted our files we need to move the **ximdex-develop** folder, that is inside our extracted folder, to the server documents root, changing the name to the one we want for our instance. You can use `mv ximdex-develop /YOUR/ROOT/ADDRESS/myximdex` to move it.

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/Selección_021.png)

In this case our root is located at **www** and our instance is named **myximdex**.

## 3. Users and permissions

To ensure a correct CMS install we need to grant the required permissions to the user used by the web server, for example, when using Apache we need to grant permissions to **www-data**:

```shell
cd /var/www/
sudo chown -R www-data:www-data myximdex
cd myximdex
sudo chmod -R ug+rw data
sudo chmod -R ug+rw logs
sudo chmod -R ug+rw conf
```

Also, optionally:

```shell
sudo chmod -R g+s data (optional)
sudo chmod g+s logs (optional)
sudo chmod g+s conf (optional)
```

## 4. Extensions install

Now we move to the CMS root folder (**myximdex** in this case) and we proceed to install using **composer** the third party repositories Ximdex needs:

```shell
cd /var/www/myximdex
composer install --no-dev
```

## 5. Database creation

We open now the connection to the chosen BDMS (MySQL or MariaDB) and insert the next SQL commands.

* We create the DB used across the CMS:

  ```sql
  CREATE DATABASE `ximdex_db`;
  ```

* We create the assigned DB user and we give him all the required DB permissions:

  ```sql
  CREATE USER 'ximdex-user'@'localhost' IDENTIFIED BY 'ximdex-pass';
  GRANT ALL PRIVILEGES ON `ximdex_db`.* TO 'ximdex-user'@'localhost' WITH GRANT OPTION;
  ```

# Web configuration

Once we finished instalation we can access the CMS through any browser at <http://YOURHOST/myximdex> (In this case <http://localhost/myximdex>) and execute the configuration tool that will load the DB, create users and install all required modules.

The landing page will greet us with a button to check if all requirements had been satisfied:

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/023.png)

Once clicked if all the requirements are fullfiled with any problems the browser will show a success notification:

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/Selección_024.png)

The "start installation" button will move to the DB configuration screen where we will be prompted for a user and pass for the database, here we must provide the ones used for the admin user of the previously created database. We press the button and select "yes" if shows a overwrite warning:

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/026.png)

Once finished we will be offered the option of creating a **unprivileged user**, for safety reasons it is highly recommended to create it and not skip this step, however you can skip it if you wish. In this case we will create one called **ximdex_user** with password **ximdex_user**:

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/Selección_027.png)

Later we will be asked the desired password for the **administrator user** of the Ximdex CMS, we can use any, but it is recommended that it be safe given the level of privileges of that user:

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/028.png)

Next we will go to the installation screen of the Ximdex modules, where simply, once loaded, we have to press the button to install these modules:

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/029.png)

Finally we will arrive at the XOwl configuration screen, where we will be asked to enter or previously generate a key for the API, this step is not currently necessary, so we can simply click on the "continue" option:

![](https://raw.githubusercontent.com/XIMDEX/resources/master/img/XCMS-install/031.png)

Once finished it will be recommended to add a line to the crontab, in this case:

```
* * * * * php /var/www/html/myximdex/bootstrap.php src/Sync/scripts/scheduler/scheduler.php
```

This step is necessary if we want the publications that we send to be published will publish correctly, otherwise Ximdex will not be able to do them, although it will still correctly work locally.
