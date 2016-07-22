<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 22/07/16
 * Time: 4:01 PM
 */
require_once 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$socket = new React\Socket\Server($loop);
$socket->on('connection', function (React\Socket\Connection $conn) {
    $conn->write("hello boy\n");

    $conn->on('data', function($data) use ($conn) {
        echo $data . "\n";
        //$conn->close();
    });

});

$socket->listen(1337);

$loop->run();