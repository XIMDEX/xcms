# Set the base image to use to Ubuntu
FROM ubuntu

# Set the file maintainer
MAINTAINER Ximdex ximdex

# Updating the repository list
RUN apt-get update

# Updating the system
RUN apt-get -y upgrade

# Installing needed packages
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server mysql-client pwgen python-setuptools curl git unzip apache2 php5 php5-gd libapache2-mod-php5 postfix wget vim curl libcurl3 libcurl3-dev php5-curl php5-xmlrpc php5-intl php5-mysql cron php5-xsl php5-enchant language-pack-es language-pack-es-base


# Cloning Ximdex CMS from GitHub in /var/www/html
RUN cd /var/www/html; rm -rf *; git clone https://github.com/XIMDEX/ximdex.git . && \
		# Setting permissions
		chown -R www-data:www-data /var/www/html && \
		chmod -R 2770 /var/www/html/data && \
		chmod -R 2770 /var/www/html/conf && \
		chmod -R 2770 /var/www/html/logs && \
		chmod -R 2770 /var/www/html/install && \
		/bin/bash -c "/usr/bin/mysqld_safe &" && \
		sleep 5 && \
		# Creating database users
		mysql -u root -e "USE mysql; GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION; UPDATE user SET password=PASSWORD(\"ximdex\") WHERE user='root'; GRANT ALL ON \`ximdex\`.* to 'ximdex'@'%' IDENTIFIED BY 'ximdex'; FLUSH PRIVILEGES;" && \
		# Creating database
		mysql -u root -pximdex -e "CREATE DATABASE IF NOT EXISTS \`ximdex\` CHARACTER SET utf8 COLLATE utf8_general_ci;" && \
		# Importing sql file
		mysql -u root -pximdex ximdex < /var/www/html/install/ximdex_data/ximdex.sql && \
		# Setting config values
		mysql -u root -pximdex ximdex -e "UPDATE Config SET ConfigValue='http://localhost:5000' WHERE ConfigKEY='UrlRoot'; UPDATE Config SET ConfigValue='/var/www/html' WHERE ConfigKEY='AppRoot'; UPDATE Users SET Login='ximdex' where IdUser = '301'; UPDATE Nodes SET Name='ximdex' where IdNode = '301'; UPDATE Users SET Pass=MD5('ximdex') where IdUser = '301'; UPDATE Config SET ConfigValue='en_US' WHERE ConfigKEY='locale'; UPDATE Config SET ConfigValue='NAMEIT' WHERE ConfigKEY='ximid';" && \
		# Config file
		cp /var/www/html/install/templates/install-params.conf.php /var/www/html/conf/ && \
		# Setting config values
		sed -i 's/##DB_HOST##/localhost/g' /var/www/html/conf/install-params.conf.php && \
		sed -i 's/##DB_PORT##/3306/g' /var/www/html/conf/install-params.conf.php && \
		sed -i 's/##DB_USER##/ximdex/g' /var/www/html/conf/install-params.conf.php && \
		sed -i 's/##DB_PASSWD##/ximdex/g' /var/www/html/conf/install-params.conf.php && \
		sed -i 's/##DB_NAME##/ximdex/g' /var/www/html/conf/install-params.conf.php && \
		sed -i 's/##XIMDEX_PATH##/\/var\/www\/html/g' /var/www/html/conf/install-params.conf.php && \
		sed -i 's/##XIMDEX_TIMEZONE##/Europe\/Madrid/g' /var/www/html/conf/install-params.conf.php && \
		sed -i 's/##XIMDEX_LOCALE##/en_US/g' /var/www/html/conf/install-params.conf.php && \
		# Setting apache to listen port 5000 
		sed -i 's/:80/:5000/g' /etc/apache2/sites-enabled/000-default.conf && \
		sed -i 's/80/5000/g' /etc/apache2/ports.conf && \
		# Setting values in php.ini
		sed -i 's/disable_functions/;disable_functions/g' /etc/php5/apache2/php.ini && \
		sed -i 's/#AddDefaultCharset/AddDefaultCharset/g' /etc/apache2/conf-enabled/charset.conf && \
		# Setting status to INSTALLED
		echo "INSTALLED" > /var/www/html/conf/_STATUSFILE && \
		# Adding task to cron
		line="* * * * * (php /var/www/html/modules/ximSYNC/scripts/scheduler/scheduler.php) >>  /var/www/html/logs/scheduler.log 2>&1" && \
		(crontab -u root -l; echo "$line" ) | crontab -u root - && \
		# Installing modules
		cd /var/www/html/install && \
		php -d memory_limit=-1 /var/www/html/install/scripts/lib/modules.php install ximLOADER 2 >nul 2>&1 && \
		bash ./module.sh install ximTOUR && \
		bash ./module.sh install ximSYNC && \
		bash ./module.sh install ximTAGS && \
		# Setting permissions (2)
		chown -R www-data:www-data /var/www/html && \
		chmod -R 2770 /var/www/html/data && \
		chmod -R 2770 /var/www/html/conf && \
		chmod -R 2770 /var/www/html/logs && \
		chmod -R 2770 /var/www/html/install

# Installing supervisor
RUN easy_install supervisor
ADD supervisord.conf /etc/supervisord.conf

EXPOSE 5000

CMD ["supervisord", "-n"]
