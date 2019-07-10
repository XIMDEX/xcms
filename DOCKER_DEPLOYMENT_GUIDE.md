## Running Ximdex CMS using Docker composer

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


2. From the Ximdex directory (i.e.: ximdex-develop, where the docker-compose.yml file is locate) run the commands:
    ```
    sudo docker-compose run composer
    sudo docker-compose up ximdex db
    ```
    That will run the containers for Apache2, PHP, MySQL and Ximdex running on the host ximdex:80 
    
3. Add to your /etc/hosts file the line:
    ```
    127.0.0.1       ximdex
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
    
If the **installation is aborted**, please use the next command to remove the .data directory at ximdex to clean the database data:
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
    

