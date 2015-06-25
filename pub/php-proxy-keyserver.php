<?php
use ctubio\HKPProxy\Keyserver;

ini_set('display_errors', TRUE);
ini_set('error_reporting', E_ALL);

require '../vendor/autoload.php';

Keyserver::getResponse()->send();
