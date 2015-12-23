<?php

/*
* Parser and Getter for Mopidy
*/

if (!defined('BASE_PATH'))
    define('BASE_PATH', dirname(dirname(__FILE__)));

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/lib/App.php';

$config = require_once BASE_PATH.'/config.php';
$localConfig = require_once BASE_PATH.'/config.local.php';
$config = array_replace_recursive($config, $localConfig);

$app = App::create($config);

$getter = new Pimusic\Getter();

$gmworker = new \GearmanWorker();

# Add default server (localhost).
$gmworker->addServer();

$gmworker->addFunction("getter", [$getter, 'work']);

print "Waiting for job...\n";
while ($gmworker->work()) {
    if ($gmworker->returnCode() != GEARMAN_SUCCESS) {
        echo "return_code: " . $gmworker->returnCode() . "\n";
        break;
    }
}