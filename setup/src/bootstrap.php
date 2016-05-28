<?php

/**
 * Main setup functions and values
 */


 // Base autoloader
spl_autoload_register(function ($class) {
    $prefix = 'Ximdex\\Setup\\';
    $base_dir = __DIR__ . '/Setup/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// twig loader
spl_autoload_register(function ($class) {
    if (0 !== strpos($class, 'Twig')) {
        return;
    }
    if (is_file($file = dirname( dirname(__FILE__)) .'/libs/'.str_replace(array('_', "\0"), array('/', ''), $class).'.php')) {
        require $file;
    }
}, true );