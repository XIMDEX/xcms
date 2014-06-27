#!/bin/bash

  #####                                           #####  
 ###   Shell script for downloading and installing   ### 
###  Open Source Semantic CMS XIMDEX (www.ximdex.com) ###
 ###       (C) 2014 Open Ximdex Evolution SL         ### 
  #####                                           #####  


# INITIAL VARS
export DEBUG=1
REPO_NAME="ximdex"
REPO_HOME="https://github.com/XIMDEX/ximdex/archive/"

#REPO_BRANCH="3.5-beta3"
REPO_BRANCH="develop"

SCRIPT1="1-MoveXimdexToDocRoot.sh"
SCRIPT2="2-AddXimdexToCrontab.txt"

# OPTIONS
DO_INSTALL=1
DO_MOVEASROOT=0

# Determining launch name
my_script_name=$( basename $0 )
my_path=$(cd $(dirname $0) && pwd -P)
my_directory=$( basename $my_path )

clear
echo "Welcome to Ximdex CMS downloader & installer"
echo "--------------------------------------------"
echo ""

# Are we running directly on a ximdex instance to be installed?
if [ -d install ] && [ -d data ] && [ -d inc ] && [ -f XIMDEX_INSTALL.sh ]; then
	echo -e "Running '$my_script_name' from '$my_path'\nin the already downloaded instance '$my_directory'" 
	echo -e "Use $my_script_name script alone in a clean directory to force the download."
	echo ""
	DO_DOWNLOAD=0
	REPO_NAME=$my_directory
	#echo "Assigning $REPO_NAME to Ximdex and running it from $my_path"
	cd $my_path/..
        if [ "$?" != 0 ]; then
		echo "Can not move to the parent directory. Exiting"	
                exit 10
        fi
	#exec $my_path/$my_script_name -i -n $my_directory
else 
	DO_DOWNLOAD=1
fi

PARENT_DIR=$(pwd -P)

# FOR AUTOMATIC INSTALLATIONS
export AUTOMATIC_INSTALL=0
export CONFIG_FILE=""
 
# Trap ctr-c to activate back "echo" in terminal
trap ending SIGINT
trap ending SIGKILL

# Subroutines (Look for SCRIPT START to go to the beginning)

function ending() {
    echo -e "\nERROR: $1 ... Exiting!"
    echo "GOODBYE!"
    stty echo
    exit 1;
}

function usage() {
    echo "Usage: $my_script_name [-h] [-d] [-i] [-a config_file] [-n ximdex_instance_name] [-t web_server_root_path] [-b branch]"
    echo "       Option -h for help"
    echo "       Option -d to only Download the instance"
    echo "       Option -i to run the Installer steps for an already downloaded instance"
    echo "       Option -a to Automatically install Ximdex using parameters from File"
    echo "       Option -t to assign the web server root (where ximdex will reside)" 
    echo "       Option -n to assign the instance Name (default: ximdex)"
    echo "       Option -b to assign the Branch to download (default: master)"
}


function LimpiaSlashes() {
	local mycad=$1
	# remove repeated slashes
	while [[ $mycad =~ "//" ]]; do
		mycad=$(echo $mycad | sed -e "s,//,/,g")
	done
	# remove last slash (collateral effect: remove ending spaces too)
	mycad=$(echo $mycad | sed -e "s,/$,,g")
	echo $mycad
}

function myquestion() {
    myquestion="$1"
    mydefault=${2-'@'}
  
    if [ $mydefault == "y" ] || [ $mydefault == "Y" ]
    then
        mydefault="Y"
        mycadena="(Y/n)"
    elif [ $mydefault == "n" ] || [ $mydefault == "N" ]
    then
        mydefault="N"
        mycadena="(y/N)"
    else
        mydefault=""
        mycadena="(y/n)"
    fi
  
    myoption=''
    ANSWER=""
    while [ "$myoption" != 'Y' ] && [ "$myoption" != 'y' ] && [ "$myoption" != 'n' ] && [ "$myoption" != 'N' ]
    do
        echo -n  "- $myquestion $mycadena? "
        read myoption
        if [ -n $mydefault ] && [ "$myoption" == '' ]; then
            myoption=$mydefault
        fi
    done
  
    if  [ "$myoption" == 'Y' ] || [ "$myoption" == 'y' ]; then
        ANSWER="Y"
    else
        ANSWER="N"
    fi
}


function CreateScriptToSetPermsAndMove() {
    XIMDEX_PATH="$REPO_NAME"
    
    declare -a arr_command=()
    
    arr_command+=("echo -n 'RUNNING GENERATED SCRIPT AS '\n")
    arr_command+=("whoami\n\n")

    if [ $XIMDEX_TARGET_DIR != $my_path ] ; then
        arr_command+=("# Verify that target directory $XIMDEX_TARGET_DIR does not exist\n\n") 
	arr_command+=("if [ -d $XIMDEX_TARGET_DIR ]; then\n")
	arr_command+=("  echo \"TARGET DIRECTORY $XIMDEX_TARGET_DIR EXISTS!!!\"\n")
	arr_command+=("  echo \"Please, remove it and run the script as root again.\"\n")
	arr_command+=("  exit 0\n")
	arr_command+=("fi\n\n\n")

	command="cd $PARENT_DIR && cp -ra ${REPO_NAME} $XIMDEX_TARGET_DIR"
	arr_command+=("$command\n")
    else
	command="#MOVE IS NOT NEEDED BECAUSE IT IS INSTALLED FROM FINAL DIRECTORY"
	arr_command+=("$command\n")
    fi

    command="chown -R ${USER_APACHE}:${GROUP_APACHE} $XIMDEX_TARGET_DIR"
    arr_command+=("$command\n")

    #command="su -c \"$XIMDEX_TARGET_DIR/install/scripts/ximdex_installer_MaintenanceTasks.sh -x $XIMDEX_TARGET_DIR\" ${USER_APACHE}"
    #arr_command+=("$command\n")

    #command="su -c \"$XIMDEX_TARGET_DIR/install/scripts/ximdex_installer_InitializeInstance.sh -x $XIMDEX_TARGET_DIR -m 1 -i 0 -p 0\" ${USER_APACHE}"
    #arr_command+=("$command\n")

    arr_command+=("exit 0\n")


    #command="chmod -R u+x ./$REPO_NAME/install/*sh ./$REPO_NAME/install/scripts/*.sh"
    #arr_command+=("$command\n")

    echo -e "\n---> MOVE IT to its final directory..."
    echo -e "\nCreating script for setting ownership and permissions for $TARGET_NAME ..."

    my_script1="$SCRIPT_PATH/$SCRIPT1"
    echo "#!/bin/bash" > $my_script1

    echo "" >> $my_script1
    echo "# BASH FILE AUTOMATICALLY GENERATED BY XIMDEX INSTALL TO BE RUN AS ROOT" >> $my_script1
    echo "" >> $my_script1
    echo -e " ${arr_command[*]}" >> $my_script1

    if [ -e $my_script1 ]; then
        echo -e "Script $my_script1 created.\n"
    else
        echo "Can not create $my_script1 file. I need writting permission here:"
        pwd -P
    fi
    
    chmod +x $my_script1
}

function RunScriptAsRoot() {
    runasroot=0
    if [ $USER_UNIX != 'root' ]; then
        echo "Running through sudo. Enter your password when requested..."
        sudo bash ./$my_script1  2> /dev/null
        if [ $? -ne 0 ]; then 
            echo "Can not run via sudo. Trying as root direclty. Enter password for root when requested..."
            su -c "bash ./$my_script1 2> /dev/null"
	else
	    runasroot=1
        fi
    fi

    if [ $runasroot == "1" ]; then
        echo "Launched!"
    fi
}

function SetDocRootForXimdex() {
	
	echo "Determining Apache user and group... "
	GROUP_APACHE="`ps -eo 'group args'|grep 'httpd\|apache' |grep 'start\|bin'|grep -v grep|grep -v root|grep -v USER|awk 'NR<=1 {print $1; }'|cut -d ' ' -f 1,1 `"
	USER_APACHE="`ps -eo 'user args'|grep 'httpd\|apache' |grep 'start\|bin'|grep -v grep|grep -v root|grep -v USER|awk 'NR<=1 {print $1; }'|cut -d ' ' -f 1,1 `"
		
	#if [ -z $USER_APACHE ]; then
	#	echo "Can not get the User for Web Server... using your 'username' instead..."
	#	USER_APACHE=$USER_UNIX
	#fi
	#if [ -z $GROUP_APACHE ]; then
	#	echo "Can not get the Group for Web Server... using your 'group' instead..."
	#	GROUP_APACHE=$GROUP_UNIX
	#fi
	
	#GROUP_APACHE=${GROUP_APACHE:-$USER_APACHE}
		
	# Determining apache directory
	apache_conf_file=`find /etc -name httpd.conf 2>/dev/null`
	apachefile=$(basename "$apache_conf_file" )
	apachedir=$(dirname "$apache_conf_file" )
	
	APACHE_DOCROOT=$(grep -rho "DocumentRoot.*" /etc/apache2 | sed "s/DocumentRoot\s\+//" | sort | uniq | tr "\n" " ")
	
	echo -e "\nXimdex is a web app and it has to be deployed under your web document root ..."
	
	# Choose parameters for final installation
	declare -a arr_dires=( $(echo "$APACHE_DOCROOT") )
	declare -a arr_DOCROOTS=()
	for dire in "${arr_dires[@]}"
	do
		if [ -d "$dire" ]; then
			arr_DOCROOTS+=("$dire")
		fi
	done
	
	docrootask=0
	if [ "${#arr_DOCROOTS[@]}" -ne "0" ]; then
		echo "We have located some suitable directories to install Ximdex."
		echo "Please, choose one of them or select 'none' to ask you for a target directory."
		echo ""
		echo "Select a target directory for Ximdex:"
		echo ""
	
		arr_DOCROOTS=( $(echo "$APACHE_DOCROOT") "None of these. I'll write the target path directly." )
		i=1
		for dire in "${arr_DOCROOTS[@]}"
		do
			echo "$i. $dire"
			i=$(expr $i + 1)
		done
	
	
		echo ""
		option=0
		while [ -z $option ] || [ $option -lt "1" ] || [ $option -gt ${#arr_DOCROOTS[@]} ] ; do
			echo -ne "Choose one [1-${#arr_DOCROOTS[@]}]: "
			read option;
		done
		if [ "$option" -eq "${#arr_DOCROOTS[@]}" ]; then
			docrootask=1
		else 
			option=$(expr $option - 1)
			DOCROOT="${arr_DOCROOTS[$option]}"
		fi
	else
		docrootask=1
	fi
	
	while [ $docrootask -eq "1" ]; do 
		echo -n "Please, type a path to the directory (web server document root): "
		read pathdoc
		if [ -d "$pathdoc" ]; then
			DOCROOT=$pathdoc
			docrootask=0
		fi
	done
	
	# remove repeated slashes
	while [[ $DOCROOT =~ "//" ]]; do
		DOCROOT=$(echo $DOCROOT | sed -e "s,//,/,g")
	done
	
	# remove last slash (collateral effect: remove ending spaces too)
	DOCROOT=$(echo $DOCROOT | sed -e "s,/$,,g")
	
}

function DetermineApacheUserGroup() {
	echo -e "\nUnix user launching the script is: $USER_UNIX (group $GROUP_UNIX)"
	echo "I will list some files at $DOCROOT to show you the user/group in use there:"
	ls -l $DOCROOT | head -10
	
	if [ ! -z $USER_APACHE ]; then
		echo -e "\n\nI've got that your Web server User is: $USER_APACHE"
		option="x"
	else 
		echo -e "\nI can not determine your Web Server User..."
		option="y"
	fi
	
	myquestion "Do you want to modify it" "N"
	
	name=""
	if [ "$ANSWER" == "Y" ]; then
		while [ -z $name ]
		do
			echo -n "Who will be the 'owner' of the files? "
			read name
			valid=$(id -gn $name 2>/dev/null)
			if [ -z $valid ]; then
				myquestion "User $name does not exist. Do you want to type it again"
			fi
		done 
		USER_APACHE=$name
	fi
	
	echo -e "File's owner will be $USER_APACHE\n"
	
	if [ ! -z $GROUP_APACHE ]; then
		echo "I've got that your Web server group is: $GROUP_APACHE"
		option="x"
	else 
		echo "I can not determine your Web Server Group..."
		option="y"
	fi
	
	myquestion "Do you want to modify it" "N"
	
	name=""
	if [ "$ANSWER" == "Y" ]; then
		while [ -z $name ]
		do
			echo -n "What will be the 'group' for the files? "
			read name
			valid=$(grep "^$name:" /etc/group 2>/dev/null)
			if [ -z $valid ]; then
				myquestion "Group $name does not exist. Do you want to type it again"
			fi
		done 
		GROUP_APACHE=$name
	fi
	
	echo -e "File's Group will be $GROUP_APACHE\n"
	echo -e "Ownership will be set to '$USER_APACHE:$GROUP_APACHE'\n"
		
	#grep -i 'DocumentRoot' httpd.conf
	#grep -i 'DocumentRoot' /etc/httpd/conf/httpd.conf
	#grep -i 'DocumentRoot' /usr/local/etc/apache22/httpd.conf
}


function CheckFinalDirectory() {
	echo "Final Ximdex directory will be -->$XIMDEX_TARGET_DIR<--"
	if [ -e $XIMDEX_TARGET_DIR ]; then 
		echo -e "A file or directory named $XIMDEX_TARGET_DIR does already exist."
		echo -e "Ximdex will be configured for this path and only will work there."
		echo -e "If you continue, you will have to overwrite that path!\n"
	
		myquestion "Do you want to continue with the installation" "Y"

		if [ "$ANSWER" == 'N' ]; then
                	echo "Aborting configuration for $REPO_NAME"
                	exit 1;
        	fi
	fi
}


# Print instructions
function PrintInstructions() {
	echo -e "\nThe main steps for installing Ximdex are:"
	echo "1.- Downloading Ximdex (branch $REPO_BRANCH) if you are in a clean directory."
	echo "    (to run only this step use option -d)"
	echo "2.- Determine parameters for installation (final directory, ...)"
	echo "    (to run only this step use -i option)"
	echo "3.- Move Ximdex into your web server document root and assign permissions." 
	echo "    This last step may require superuser privileges."
	echo "    Script $SCRIPT1 will be generated to be run as root."
	echo ""
}

# STEP Download from Github
function Step_Download() {
	if [ $REPO_BRANCH != "develop" ] && [ $REPO_BRANCH != "master" ]; then
		# it is a TAG, add v to the name
		REPO_HOME="${REPO_HOME}v"
	fi
	ZIP_FILE="$REPO_BRANCH.zip"
	REPO_FILE="$REPO_HOME$ZIP_FILE"
	#REPO_FILE="https://github.com/XIMDEX/ximdex/archive/f555dcf3f7d9360d124afd1372bef6c7591c37bc.zip"
	echo "---> DOWNLOAD Ximdex ..."
	echo "Downloading Ximdex ($REPO_BRANCH branch) to directory '$REPO_NAME'"
	echo "located at $LOCALPATH from $REPO_FILE..."
	echo ""
	echo "Creating directory $REPO_NAME ... "
	mkdir $REPO_NAME
	if [ $? -ne 0 ]; then echo -e "\nCan not create directory ($REPO_NAME) for Ximdex!\nPlease, remove it or choose another name (-n option)." ; exit 1; fi
	rmdir $REPO_NAME
	echo "done!"
	
	echo -n "Downloading branch $REPO_BRANCH from $REPO_HOME ... "
	wget "$REPO_FILE" -O $ZIP_FILE
	if [ $? -ne 0 ]; then echo -e "\nERROR: Can not Download Ximdex ($REPO_FILE) from Github"; exit 1; fi
	
	echo -n "Unzipping file $ZIP_FILE ... "
	unzip $ZIP_FILE > /dev/null
	if [ $? -ne 0 ]; then echo -e "\nERROR: Can not unzip file $ZIP_FILE"; exit 1; fi
	echo "done!"
	
	mv "ximdex-$REPO_BRANCH" $REPO_NAME
	rm $ZIP_FILE
}

function DieIfNotInstallable() {
	cd $REPO_NAME 
	if [ $? -ne 0 ]; then 
		echo -e "\nCan not enter $REPO_NAME directory!" 
		exit 1 
	elif [ -d install ] && [ -d data ] && [ -d inc ]; then
		echo -e "'$REPO_NAME' looks a Ximdex instance."
	else
		echo -e "'$REPO_NAME' does not look a Ximdex instance. Exiting!"
		exit 1
	fi

	cd ..
}

function SetInstallStatus() {
	if [ -n $1 ]; then
		echo $1 > $STATUSFILE || ending "CAN NOT WRITE TO $STATUSFILE"
	fi
}

function GetInstallStatus() {
	if [ -n $STATUSFILE ] && [ -f $STATUSFILE ]; then
		cat $STATUSFILE
	else
		echo ""
	fi
}

# STEP Checking ximdex dependencies
function Step_Dependencies() {
	echo "STEP1: Checking required components as PHP, MySQL, etc."
	$(chmod +x $SCRIPT_PATH/scripts/ximdex_installer_CheckDependencies.sh)
	( $SCRIPT_PATH/scripts/ximdex_installer_CheckDependencies.sh )
	result="$?"
	if [ "$result" != 0 ]; then
		echo "Some dependencies for Ximdex are not on your system."
		myquestion "Do you want to continue with the installation" "Y"
		if [ "$ANSWER" == 'N' ]; then
			echo "Aborting configuration for $REPO_NAME"
			exit 1;
		fi
	fi
}

# STEP Database creation
function Step_CreateDB() {
	echo "STEP2: Creating Database"
	$(chmod +x $SCRIPT_PATH/scripts/ximdex_installer_CreateDatabase.sh)
	( $SCRIPT_PATH/scripts/ximdex_installer_CreateDatabase.sh -i )
	result="$?"
	if [ "$result" != 0 ]; then
		echo "It seems that the Database has not been created"
	        myquestion "Do you want to continue with the installation" "Y"
	        if [ "$ANSWER" == 'N' ]; then
	                echo "Aborting configuration for $REPO_NAME"
	                exit 1;
	        fi
	fi
}


# STEP Setting Ximdex internal parameters
function Step_Configurator() {
	echo "STEP3: Setting Ximdex working parameters in config ($REPO_NAME/conf)"
	$(chmod +x $SCRIPT_PATH/scripts/ximdex_installer_Configurator.sh)
	( $SCRIPT_PATH/scripts/ximdex_installer_Configurator.sh -i -t -n -w -x "$XIMDEX_TARGET_DIR" )
	result="$?"
	if [ "$result" != 0 ]; then
		echo "Configuration have not ended correclty."
	        myquestion "Do you want to continue with the installation" "Y"
	        if [ "$ANSWER" == 'N' ]; then
	                echo "Aborting configuration for $REPO_NAME"
	                exit 1;
	        fi
	fi
}


# STEP Setting Ximdex internal components
function Step_Maintenance() {
	echo "STEP4: Setting Ximdex components"
	$(chmod +x $SCRIPT_PATH/scripts/ximdex_installer_MaintenanceTasks.sh)
	
	( $SCRIPT_PATH/scripts/ximdex_installer_MaintenanceTasks.sh -x "$XIMDEX_TARGET_DIR" )
	result="$?"
	if [ "$result" != 0 ]; then
		echo "Component paramerization have not ended correclty."
	        myquestion "Do you want to continue with the installation" "Y"
	        if [ "$ANSWER" == 'N' ]; then
	                echo "Aborting configuration for $REPO_NAME"
	                exit 1;
	        fi
	fi
}


# STEP Setting Ximdex projects
function Step_ProjectsAndCrontab() {
	echo "STEP5: Installing some projects and configuring crontab"
	$(chmod +x $SCRIPT_PATH/scripts/ximdex_installer_InitializeInstance.sh)
	
	( $SCRIPT_PATH/scripts/ximdex_installer_InitializeInstance.sh -x "$XIMDEX_TARGET_DIR" -m 0 -i 0 -p 0 -c $SCRIPT2)
	result="$?"
	if [ "$result" != 0 ]; then
		echo "Projects may have been not installed." 
	        myquestion "Do you want to continue with the installation" "Y"
	        if [ "$ANSWER" == 'N' ]; then
	                echo "Aborting configuration for $REPO_NAME"
	                exit 1;
	        fi
	fi
	
	echo "If you want to publish into remote servers in the cloud, ..."
	echo "Ximdex decoupled publishing system has to be periodically launched ..."
	echo "File $SCRIPT2 has these commands you should add to crontab:"
	( cat $SCRIPT2 )
}

# STEP launch mv to final destination as root 
function Step_LaunchAsRoot() {

	if [ $AUTOMATIC_INSTALL = 1 ]; then
		if [ "$DO_MOVEASROOT" = 1 ]; then
			ANSWER='Y'
		else
			ANSWER='N'
		fi
	else
		myquestion "Do you want me to run the script $SCRIPT1 as root"
	fi
	
	runasroot=0
	if [ "$ANSWER" == 'Y' ]; then
		RunScriptAsRoot
	else
		echo -e "\n\nYOU HAVE TO RUN THE SCRIPT $SCRIPT1 AS ROOT TO CONTINUE !!"
		echo -e "(or run manually the commands in that script)\n"
	fi
	
	if [ $runasroot == "1" ]; then
	    echo -e "\n---> CONFIGURE Ximdex from your web browser..."
	fi

	echo -e "Once moved to its final place, visit your Ximdex instance to end configuring it."
	echo -e "by pointing your web browser to your just installed Ximdex CMS instance URL "
	echo -e "(i.e.: http://YOURHOST/${REPO_NAME} or http://localhost/${REPO_NAME}) to run the "
	echo -e "configuration tool that will load the database and install Ximdex's modules."
	echo -e "\nThanks for installing Ximdex. Write to help@ximdex.org if you need help.\n"
	
	if [ -e install ]; then 
	echo "********************************************************************************"
	echo "Ximdex has been downloaded as:"
	pwd -P
	echo ""
	echo "To end the installation:"
	echo "1.- Move it to your Web Server Root (i.e.: 'sudo mv $REPO_NAME /var/www/')"
	echo "2.- Execute 'sudo /var/www/$REPO_NAME/install/install.sh' to configure it..."
	echo "********************************************************************************"
	fi
}


# SCRIPT START


while getopts 'a:hdin:b:t:' OPTION;
do
    case $OPTION in
        a)
	    AUTOMATIC_INSTALL=1
	    CONFIG_FILE="$OPTARG"
            ;;
        t)
	    REPO_ROOT=$OPTARG
            ;;
        n)
	    REPO_NAME=$OPTARG
	    TARGET_NAME=$REPO_NAME
            ;;
	b)
	    REPO_BRANCH=$OPTARG
            ;;
	i)
	    DO_DOWNLOAD=0
            ;;
	d)
	    DO_INSTALL=0
            ;;
        h)
	    usage
	    exit 0
	    ;;
        *)
	    echo "ERROR"
	    usage
	    exit 0
	    ;;
    esac
done

if [ "$AUTOMATIC_INSTALL" = 1 ]; then
	if [ -f "$CONFIG_FILE" ]; then
		echo "AUTOMATIC mode starting..."
	else
		echo "Can not find $CONFIG_FILE for automatic installation. Exiting!"
		exit 90
	fi
else
	echo "The name for your Ximdex instance will be '$REPO_NAME'"
	myquestion "Do you want to modify it" "N"
	name=""
	if [ "$ANSWER" == "Y" ]; then
		echo -n "What is the Name for your Ximdex instance [$REPO_NAME]? "
		read name

	if [ -z $name ];then
		name=$REPO_NAME
	fi
		REPO_NAME=$name
	fi
fi

if [ -z $REPO_NAME ] ; then
    echo "$REPO_NAME is not valid. Exiting!"
    exit 1
fi

if [ -z $REPO_BRANCH ] ; then
    echo "$REPO_BRANCH is not valid. Exiting!"
    exit 1
fi

# Initialize vars
if [ -z $TARGET_NAME ]; then
	TARGET_NAME=$REPO_NAME
fi
SCRIPT_PATH="./${REPO_NAME}/install"
SCRIPT_CONFIG_PATH="./${REPO_NAME}/conf"
STATUSFILE="$SCRIPT_CONFIG_PATH/_STATUSFILE"
LOCALPATH=$( pwd -P )
USER_UNIX=`whoami`
GROUP_UNIX=`id -gn`

# printing instructions
PrintInstructions

# STEP_DOWNLOAD
if [  $DO_DOWNLOAD -ne 0 ]; then
	Step_Download
else
	echo -e "Downloading of Ximdex skipped."
fi

# Check if the directory is accesible and if it seems a ximdex
DieIfNotInstallable

# Set status to Downloaded
mystatus=$( GetInstallStatus )
[ -z $mystatus ] && SetInstallStatus "DOWNLOADED" && mystatus=$( GetInstallStatus )


if [ $AUTOMATIC_INSTALL = 0 ]; then
	echo -e "\nThe installer can be run in automatic mode with -a option."
	echo "An example of a setup file can be located at install/templates/setup.conf"
	echo "Edit it and run this script with the options: -i -a yoursetupfile"
	echo -en "\nPRESS ENTER TO CONTINUE"
	read
	clear
fi

if [  $DO_INSTALL -eq 0 ]; then
        echo -e "Configuration steps optionally skipped. Exiting!"
	exit 0
fi

if [ "$mystatus" == "DOWNLOADED" ] || [ "$mystatus" == "INIT" ] ; then
    echo -e "---> SET directory and web server user..."
    echo -e "\nXimdex instance is suitable for installation. Starting configuration:"
else
    echo -e "\n$REPO_NAME has traces of a previous installation ended at $mystatus step."
    echo "Copy this script $my_script_name to a clean directory to download&install if you want to start with a new instance." 
    echo ""
    myquestion "Do you want to continue with the installation" "Y"
    if [ "$ANSWER" == 'N' ]; then
       echo "Aborting configuration for $REPO_NAME"
        exit 1;
    fi
fi

if [ "$AUTOMATIC_INSTALL" = 1 ]; then
	echo "Starting automatic install"
	. $SCRIPT_PATH/scripts/ximdex_installer_LoadAutomaticParams.sh "$CONFIG_FILE"
        result="$?"
        if [ "$result" != 0 ];
        then
                exit $result
        fi

	# Determine params from setup
	mycad=$( LimpiaSlashes "$XIMDEX_PARAMS_PATH")
	DOCROOT=${mycad%/*}
	REPO_ROOT=$DOCROOT
	TARGET_NAME=${mycad##*/}
fi

if [ "$AUTOMATIC_INSTALL" = 0 ]; then
	# determine web server info (user, group, document root), path document root
	if [ -z $REPO_ROOT ] || [ ! -d "$REPO_ROOT" ] ; then
		SetDocRootForXimdex
		REPO_ROOT=$DOCROOT
	fi

	DetermineApacheUserGroup
fi
echo "Directory [$DOCROOT] will store Ximdex instance [$TARGET_NAME]."

# Determine FINAL DIRECTORY where Ximdex will be as web application
XIMDEX_TARGET_DIR=$( LimpiaSlashes "$DOCROOT/$TARGET_NAME")
CheckFinalDirectory

# Set Permission/owners to local username to run configurator
echo "Setting temporary owners to ${USER_UNIX}:${GROUP_UNIX} for '$REPO_NAME'"
chown -R ${USER_UNIX}:${GROUP_UNIX} ./${REPO_NAME}

echo "Setting permissions for writable directories"
echo ""
$(chmod -R 2770 ${REPO_NAME}/data)
$(chmod -R 2770 ${REPO_NAME}/logs)
$(chmod -R 2770 ${REPO_NAME}/install)

# Set permission to config files
if [ -f ${REPO_NAME}/conf/install-modules.conf ]; then
    $(chmod -R 770 ${REPO_NAME}/conf/install-modules.conf)
fi

# Launching steps
if [ "$AUTOMATIC_INSTALL" = 1 ]; then
    Step_Dependencies && SetInstallStatus "CHECKED"
    Step_CreateDB && SetInstallStatus "CREATED_DB"
    Step_Configurator && SetInstallStatus "CONFIGURED"
fi

CreateScriptToSetPermsAndMove && Step_LaunchAsRoot


#if [ "$AUTOMATIC_INSTALL" = 1 ]; then
#    Step_Maintenance
#    Step_ProjectsAndCrontab
#fi

