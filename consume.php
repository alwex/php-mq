<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 27/07/16
 * Time: 2:35 PM
 */

require_once 'vendor/autoload.php';

$consumer = new \PhpMQ\Core\Consumer('QP');
$consumer->run();