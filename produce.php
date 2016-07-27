<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 27/07/16
 * Time: 2:19 PM
 */

require_once 'vendor/autoload.php';

$producer = new \PhpMQ\Core\Producer();
for ($i=0; $i <= 10000; $i++) {
    $producer->post('QP', str_repeat('a', rand(10, 300)), 1);
}