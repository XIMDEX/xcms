<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

use Ximdex\Helpers\ServerConfig;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;

require_once(APP_ROOT_PATH.'/install/managers/messages/ConsoleMessagesStrategy.class.php');
require_once(APP_ROOT_PATH.'/install/managers/messages/WebMessagesStrategy.class.php');


/**
 * Manager for install process
 */
class InstallManager
{

    //CONSTANTS FOR INSTALL MODE
    const WEB_MODE = "web";
    const CONSOLE_MODE = "console";
    const STATUSFILE = "/conf/_STATUSFILE";

    const INSTALL_CONF_FILE = "install.xml";
    const INSTALL_PARAMS_TEMPLATE = "/install/templates/install-params.conf.php";
    const INSTALL_PARAMS_FILE = "/conf/install-params.conf.php";
    const LAST_STATE = "INSTALLED";
    const FIRST_STATE = "INIT";
    
    const MIN_PHP_VERSION = '7.1';

    protected $mode = ""; //install mode.
    protected $installMessages = null;
    protected $installConfig = null;
    public $currentState;
    public $currentStep;

    /**
     * Construct method
     * @param string $mode Install mode: Web or console
     */
    public function __construct($mode = self::CONSOLE_MODE)
    {
        $this->mode = $mode;
        $messageClassName = $this->mode . "MessagesStrategy";
        $this->installMessages = new $messageClassName();
        $installConfFile = APP_ROOT_PATH . "/install/conf/" . self::INSTALL_CONF_FILE;

        $this->installConfig = new DomDocument();
        $this->installConfig->load($installConfFile);
        $this->currentState = $this->getCurrentState();
    }

    /**
     * Get Steps from config xml.
     * @return array Associative array
     */
    public function getSteps()
    {
        $xpath = new DomXPath($this->installConfig);
        $query = "/install/steps/step";
        $steps = $xpath->query($query);
        $result = array();
        foreach ($steps as $i => $step) {
            $auxStepArray = array();
            foreach ($step->attributes as $attribute) {
                $auxStepArray[$attribute->name] = $attribute->value;
            }
            $auxStepArray["description"] = $step->nodeValue;
            if ($auxStepArray["state"] == strtolower($this->currentState))
                $this->currentStep = $i;
            $result[] = $auxStepArray;
        }

        return $result;
    }

    /**
     * Get status from _STATUSFILE
     * @return string get Status
     */
    public function getCurrentState()
    {
        $statusFile = XIMDEX_ROOT_PATH . self::STATUSFILE;
        if (!file_exists($statusFile))
            return false;
        return trim(strtolower(FsUtils::file_get_contents($statusFile)));
    }

    /**
     * Check if Ximdex is already installed.
     * @return boolean true if is already installed.
     */
    public function isInstalled()
    {
        $currentState = $this->getCurrentState();
        if (!$currentState)
            return false;

        return $currentState == strtolower(self::LAST_STATE);
    }

    public function createStatusFile()
    {
        FsUtils::file_put_contents(XIMDEX_ROOT_PATH . self::STATUSFILE, self::FIRST_STATE);
    }

    /**
     * Write the next step into _STATUSFILE file
     */
    public function nextStep()
    {

        $newState = "";
        $steps = $this->getSteps();
        $nextStep = $this->currentStep + 1;
        if (count($steps) > $nextStep) {
            $newState = $steps[$nextStep]["state"];

        } else {
            $newState = self::LAST_STATE;
        }
        FsUtils::file_put_contents(XIMDEX_ROOT_PATH . self::STATUSFILE, strtoupper($newState));
    }

    /**
     * Write the prev step into _STATUSFILE file
     */
    public function prevStep()
    {

        $newState = "";
        $steps = $this->getSteps();
        $prevStep = $this->currentStep - 1;
        if ($prevStep < 0)
            $prevStep = 0;
        $newState = $steps[$prevStep]["state"];
        FsUtils::file_put_contents(XIMDEX_ROOT_PATH . self::STATUSFILE, strtoupper($newState));

    }

    public function getModulesByDefault($default = true)
    {
        $query = "/install/modules/module";
        $query .= $default ? "[@default='1']" : "[not(@default) or @default='0']";
        return $this->getModulesByQuery($query);
    }

    public function getModulesByQuery($query)
    {
        $result = array();
        $xpath = new DomXPath($this->installConfig);
        $modules = $xpath->query($query);

        foreach ($modules as $module) {
            $auxModuleArray = array();
            foreach ($module->attributes as $attribute) {
                $auxModuleArray[$attribute->name] = $attribute->value;
            }
            $auxModuleArray["description"] = $module->nodeValue;
            $result[] = $auxModuleArray;

        }
        return $result;
    }

    public function getAllModules()
    {
        $query = "/install/modules/module";
        return $this->getModulesByQuery($query);
    }

    public function getModuleByName($name, $exclude_alias = true)
    {
        $extra_query = $exclude_alias ? "" : " or @alias='{$name}'";
        $query = "/install/modules/module[@name='{$name}' $extra_query]";
        return $this->getModulesByQuery($query);
    }


    public function initialChecking()
    {
        $result = array();
        $result[] = $this->checkInstanceGroup();
        $result[] = $this->checkFilePermissions();
        $result[] = $this->checkRequiredPackages();
        $result[] = $this->checkPHPVersion();
        $result[] = $this->checkRequiredPHPExtensions();
        $result[] = $this->checkRecommendedPHPExtensions();
        $result[] = $this->checkDisabledFunctions();
        return $result;
    }

    private function checkRequiredPackages()
    {
        $result = array();
        $result["name"] = "OpenSSL";
        exec('openssl version', $res);
        if (count($res) > 0) {
            $result["state"] = "success";
        } else {
            $result["state"] = "error";
            $result["messages"][] = "OpenSSL package is needed";
            $result["help"][] = "Please install the openssl package on your system.";
        }
        return $result;
    }

    private function checkPHPVersion()
    {
        $version = phpversion();
        $minPHPVersion = self::MIN_PHP_VERSION;
        $version = explode(".", $version);
        $version = "{$version[0]}.{$version[1]}";
        $result = array();
        $result["name"] = "PHP version";
        if ($version >= $minPHPVersion) {
            $result['state'] = 'success';
        } else {
            $result['state'] = 'warning';
            $result['messages'][] = "Recommended PHP $minPHPVersion or higher";
            $result['help'][] = 'Remember that you can experiment serious problems to run this application propertly if you don have at least that version of PHP';
        }
        return $result;
    }

    private function checkRequiredPHPExtensions()
    {
    	$result = array();
    	$result["name"] = 'PHP required extensions';
    	$result['state'] = 'success';
    	$current = array_merge(get_loaded_extensions());
    	$required = ['PDO','pdo_mysql'];
    	foreach ($required as $item)
    	{	
    		if (!in_array($item, $current))
    		{
    			$result['state'] = 'error';
    			$result['messages'][] = strtoupper($item) . ' extension for PHP is required';
    			$result['help'][] = 'Please, install it following the instructions in the INSTALLATION.md file';
    		}
    	}
    	return $result;
    }
    
    private function checkRecommendedPHPExtensions()
    {
        // Apache modules cannot be loaded in Nginx and is not necessary in the recommended PHP extension
        //$modules = array_merge(apache_get_modules(), get_loaded_extensions());
        $modules = get_loaded_extensions();
        $recommendedModules = ['xsl', 'curl', 'gd', 'mcrypt'];
        if (!isset($_SERVER['DOCKER_CONF_HOME']))
        {
            $recommendedModules[] = 'enchant';
        }
        $result["state"] = 'success';
        $result["name"] = 'PHP recommended extensions';
        foreach ($recommendedModules as $recommendedModule) {
            if (!in_array($recommendedModule, $modules)) {
                $result['state'] = 'warning';
                $result['messages'][] = 'PHP ' . strtoupper($recommendedModule) . ' extension is recommended';
                $result['help'][] = 'Please, remember to install it as soon its possible following the instructions in the INSTALLATION.md file. Maybe the web server must be restarted';
            }
        }
        return $result;
    }

    private function checkDisabledFunctions()
    {
        $ximdexServerConfig = new ServerConfig();
        //Checking pcntl_fork function is not disabled
        $result["state"] = 'success';
        $result["name"] = 'Disabled functions';
        if ($ximdexServerConfig->hasDisabledFunctions()) {
            $result['state'] = 'warning';
            $result['messages'][] = 'Disabled pcntl_fork and pcntl_waitpid functions are recommended';
            $result['help'][] = 'Please, check php.ini file';
        }

        return $result;
    }

    /**
     * Checking install parameters
     */
    public function checkFilePermissions()
    {
		$result = array();
        $result["state"] = "success";
        $result["name"] = "File permission";
        $filesToCheck = array("/data", "/logs", "/conf");
        $result['messages'] = array();
        $result['help'] = array();
        foreach ($filesToCheck as $file) {
            if (!file_exists(XIMDEX_ROOT_PATH . $file)) {
                $result['state'] = "error";
                $exception['messages'][] = "$file not found.";
            } else if (!$this->isWritable(XIMDEX_ROOT_PATH . $file)) {
            	$result['state'] = "error";
            	$result['messages'][] = "Write permissions on $file directory required. Please, execute this command:";
            	$result['help'][] = "sudo chmod -R g+ws " . XIMDEX_ROOT_PATH . $file;
            }
        }
        
        return $result;
    }

    private function isWritable($file)
    {
        $textToCheck = "TEST";
        if (!is_writable($file))
            return false;
        if (is_dir($file)) { //Try to create and read in a file
            $dummyFile = $file . "/.dummy";
            $result = file_put_contents($dummyFile, $textToCheck);
            if ($result === FALSE)
                return $result;
            $result =( file_get_contents($dummyFile) === $textToCheck );
            $result = $result && file_exists($dummyFile) && unlink($dummyFile);
            return $result == $textToCheck;
        }
        return true;
    }


    public function checkInstanceGroup()
    {
        return $this->checkGroup(XIMDEX_ROOT_PATH);
    }


    public function checkGroup($file)
    {
        $result["state"] = "success";
        $result["name"] = "File permission";
        
        // if the project is running under a Docker instance, it avoid this step
        if (isset($_SERVER['DOCKER_CONF_HOME']))
            return $result;
        
        $groupId = posix_getgroups();
        $groupName = posix_getgrgid($groupId[0]);
        $ximdexGroupId = filegroup($file);
        $ximdexGroupName = posix_getgrgid($ximdexGroupId);

        if (!in_array($ximdexGroupId, $groupId)) {
            $result["state"] = "error";
            $result["messages"][] = "You must set the {$groupName["name"]} group for your project directory files instead of {$ximdexGroupName["name"]}. Please, execute this command:";
            $result["help"][] = "sudo chgrp -R {$groupName["name"]} " . $file;

        }

        return $result;
    }


    public function setInstallParams($host, $port, $bdName, $user, $pass)
    {

        if (file_exists(XIMDEX_ROOT_PATH . self::INSTALL_PARAMS_FILE)) {
            rename(XIMDEX_ROOT_PATH . self::INSTALL_PARAMS_FILE, XIMDEX_ROOT_PATH . self::INSTALL_PARAMS_FILE . "bck_" . date('Ymd_his'));
        }

        $content = FsUtils::file_get_contents(APP_ROOT_PATH . self::INSTALL_PARAMS_TEMPLATE);
        FsUtils::file_put_contents(XIMDEX_ROOT_PATH . self::INSTALL_PARAMS_FILE, $content);

        $result = $this->setSingleParam("##DB_HOST##", $host);
        $result = $result && $this->setSingleParam("##DB_PORT##", $port);
        $result = $result && $this->setSingleParam("##DB_USER##", $user);
        $result = $result && $this->setSingleParam("##DB_PASSWD##", $pass);
        $result = $result && $this->setSingleParam("##DB_NAME##", $bdName);
        $result = $result && $this->setSingleParam("##XIMDEX_TIMEZONE##", $this->getTimeZone());

        return $result;
    }

    public function setSingleParam($oldValue, $newValue)
    {
        $content = FsUtils::file_get_contents(XIMDEX_ROOT_PATH . self::INSTALL_PARAMS_FILE);
        $content = str_replace($oldValue, $newValue, $content);
        return FsUtils::file_put_contents(XIMDEX_ROOT_PATH . self::INSTALL_PARAMS_FILE, $content);
    }

    private function getTimeZone()
    {
        $result = "Europe/Madrid";
        if (file_exists("/etc/timezone") && is_readable("/etc/timezone")) {
            $result = file_get_contents("/etc/timezone");
        } else if (date_default_timezone_get()) {
            $result = date_default_timezone_get();
        }
        return trim($result);
    }

    public function setLocale($lang)
    {
        $db = new \Ximdex\Runtime\Db();
        $db->execute("UPDATE Locales SET Enabled='1' where Code = '$lang'");
    }

    public function insertXimdexUser($pass)
    {
        $db = new \Ximdex\Runtime\Db();
        $db->execute("UPDATE Users SET Pass=MD5('$pass') where IdUser = '301'");
    }

    public function setXid()
    {
        $restProvider = new \Ximdex\Rest\RESTProvider();
        $hostName = $_SERVER["HTTP_HOST"];
        $url = "http://xid.ximdex.net/stats/getximid.php?host=$hostName";
        $response = $restProvider->getHttp_provider()->get($url);
        $result = isset($response["data"]) ? $response["data"] : $hostName . uniqid($hostName);
        App::setValue("ximid", $result, true );
    }

    public function setLocalXid()
    {
        $hostName = $_SERVER["HTTP_HOST"];
        $uniqid = $hostName . "_" . uniqid();
        App::setValue("ximid", $uniqid , true );
    }

    public function setApiKey()
    {
        $random = md5(rand());
        exec('openssl enc -aes-128-cbc -k "' . $random . '" -P -md sha1', $res);
        $key = explode("=", $res[1])[1];
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-128-CBC'));
        $db = new \Ximdex\Runtime\Db();
        $db->execute("UPDATE Config SET ConfigValue='" . $key . "' where ConfigKey='ApiKey'");
        $db->execute("UPDATE Config SET ConfigValue='" . $iv . "' where ConfigKey='ApiIV'");
    }
}