<?php

/*
The purpose of this script is change the domain host URL and a optional different urlFrontController
Usage: php public_xmd/install/rename_url_root.php http://nuevaURL [urlFront]
IMPORTANT: must be called from Ximdex root directory
*/

use Colors\Color;
use Ximdex\Models\Node;
use Ximdex\Runtime\App;

include 'bootstrap.php';

global $argc, $argv;

$color = new Color();
if (!isset($argv) or !$argv or $argc < 2) {
    echo $color('ERROR: The parameter URL has not been specified')->red()->bold() . PHP_EOL;
    exit();
}
$url = $argv[1];
if (filter_var($url, FILTER_VALIDATE_URL) === false) {
    echo $color('ERROR: The parameter URL ' . $url . ' is not valid (ie. http://ximdex.com)')->red()->bold() . PHP_EOL;
    exit();
}

// parse the URL given into its host name and path
$data = parse_url($url);
$values = [];
$values['UrlHost'] = $data['scheme'] . '://' . $data['host'] . ((isset($data['port'])) ? ':' . $data['port'] : '');
if (isset($data['path'])) {
    $values['UrlRoot'] = rtrim($data['path'], '/');
} else {
    $values['UrlRoot'] = '';
}
if (isset($argv[2])) {
    $values['UrlFrontController'] = $argv[2];
} else {
    $values['UrlFrontController'] = '/public_xmd';
}

// Change the database config values
foreach ($values as $key => $value) {
    $sql = 'update Config set ConfigValue = \'' . $value . '\' where ConfigKey = \'' . $key . '\'';
    $dbConn = new \Ximdex\Runtime\Db();
    $res = $dbConn->exec($sql);
    if ($res === false) {
        echo $color('ERROR: Cannot update Config data [' . $key . ']')->red()->bold();
        $errors = $dbConn->errorInfo();
        if (isset($errors[2]))
            echo $color(': ' . $errors[2])->red();
        echo PHP_EOL;
        exit();
    }
    echo 'Database value ' . $key . ' changed to ' . $value . PHP_EOL;
}

// Reload the config values
foreach ($values as $key => $value) {
    App::setValue($key, $value);
}

// Regenerate the templates_include.xsl with the new URL
$ximdex = new Node(10000);
$xsltNode = new \Ximdex\NodeTypes\XsltNode($ximdex);
$res = $xsltNode->reload_templates_include($ximdex);
if (!$res) {
    echo $color('ERROR: In reloading templares include files:')->red()->bold();
    foreach ($xsltNode->messages->messages as $error) {
        echo PHP_EOL . $color($error['message'])->red();
    }
    echo PHP_EOL;
    exit();
}
echo 'XSL templates content regenerated' . PHP_EOL;

// Restart the scheduler batch
echo 'Waiting to restart the scheduler daemon process...';
if (!@touch(XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/scheduler.stop')) {
    echo PHP_EOL . $color('WARNING: Cannot create the scheduler.stop file; please, restart process manually')->yellow() . PHP_EOL;
} else {
    $cont = 1;
    do {
        sleep(2);
        if (!file_exists(XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/scheduler.lck')) {
            echo PHP_EOL . 'Process stopped. It will restart soon';
            break;
        }
        $cont++;
        echo '.';
    }
    while($cont < 20);
    echo PHP_EOL;
    if ($cont == 20) {
        echo $color('WARNING: Cannot stop the scheduler process. Please, restart it manually')->yellow() . PHP_EOL;
    }
    if (!@unlink(XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/scheduler.stop')) {
        echo $color('WARNING: Cannot delete the scheduler.stop file; please, start process manually')->yellow() . PHP_EOL;
    }
}
echo $color('Host configuration changed to ' . $values['UrlHost'] . $values['UrlRoot'] . ' sucessfully')->green() . PHP_EOL;
echo $color('Remember to change the existing publication servers in each project and crontab schedulers')->yellow() . PHP_EOL;
