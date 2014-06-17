<?php
require_once("vendor/autoload.php");

use DatingVIP\IRC\Connection;
use DatingVIP\xaero\Robot;
use DatingVIP\xaero\Logger;
use DatingVIP\xaero\Manager;
use DatingVIP\xaero\Config;

set_time_limit(0);

/* load configuration from command line */
$config = new Config($argv);

/* open connection to server */
$connection = new Connection
	($config["server"]["hostname"], $config["server"]["port"], $config["server"]["ssl"]);

/* set logger object if not set quiet on command line */
if (!$config->hasFlag("quiet")) {
    $connection->setLogger(new Logger());
}

/* create robot with default pool */
$robot = new Robot(
    $connection,
    new Pool($config["threads"]),
    new Manager());

/* login, join channels and enter main loop */
$robot->login($config["nick"])
	->join($config["channel"])
	->loop();
?>
