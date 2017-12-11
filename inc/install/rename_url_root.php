<?php
    // The purpose of this script is change the domain host URL
    // IMPORTANT: must be called from Ximdex root directory
    
    use Ximdex\Models\Node;
    use Ximdex\Runtime\App;

    if (!isset($argv) or !$argv or count($argv) != 2)
    {
        echo 'ERROR: The parameter URL has not been specified' . "\n";
        exit();
    }
    $url = trim($argv[1]);
    if (filter_var($url, FILTER_VALIDATE_URL) === false)
    {
        echo 'ERROR: The parameter URL ' . $url . ' is not valid (ie. http://ximdex.com)' . "\n";
        exit();
    }

    // parse the URL given into its host name and path
    $data = parse_url($url);
    $values = [];
    $values['urlHost'] = $data['scheme'] . '://' . $data['host'] . ((isset($data['port'])) ? ':' . $data['port'] : '');
    if (isset($data['path']))
        $values['urlRoot'] = rtrim($data['path'], '/');
    else
        $values['urlRoot'] = '';
    
    include_once 'bootstrap/start.php';
    
    // change the database config values
    foreach ($values as $key => $value)
    {
        $sql = 'update Config set ConfigValue = \'' . $value . '\' where ConfigKey = \'' . $key . '\'';
        $res = $dbConn->exec($sql);
        if ($res === false)
        {
            echo 'ERROR: Cannot update Config data [' . $key . ']';
            $errors = $dbConn->errorInfo();
            if (isset($errors[2]))
                echo ': ' . $errors[2];
            echo "\n";
            exit();
        }
    }
    echo 'Database values changed' . "\n";
    
    // reload the config values
    App::setValue('UrlHost', $values['urlHost']);
    App::setValue('UrlRoot', $values['urlRoot']);
    
    // regenerate the templates_include.xsl with the new URL
    $ximdex = new Node(10000);
    $xsltNode = new xsltnode($ximdex);
    $res = $xsltNode->reload_templates_include($ximdex);
    if (!$res)
    {
        echo 'ERROR: In reloading templares include files:';
        foreach ($xsltNode->messages->messages as $error)
            echo "\n" . $error['message'];
        echo "\n";
        exit();
    }
    echo 'XSL templates content regenerated' . "\n";
    
    // restart the scheduler batch
    echo 'Waiting to restart the scheduler daemon process...';
    if (!@touch(XIMDEX_ROOT_PATH . '/data/tmp/scheduler.stop'))
        echo "\n" . 'WARNING! Cannot create the scheduler.stop file; please, restart process manually' . "\n";
    else
    {
        $cont = 1;
        do
        {
            sleep(2);
            if (!file_exists(XIMDEX_ROOT_PATH . '/data/tmp/scheduler.lck'))
            {
                echo "\n" . 'Process stopped. It will restart soon';
                break;
            }
            $cont++;
            echo '.';
        }
        while($cont < 150);
        echo "\n";
        if (!@unlink(XIMDEX_ROOT_PATH . '/data/tmp/scheduler.stop'))
            echo 'WARNING! Cannot delete the scheduler.stop file; please, start process manually' . "\n";
    }
    
    echo 'Host configuration changed to ' . $values['urlHost'] . $values['urlRoot'] . ' sucessfully' . "\n";