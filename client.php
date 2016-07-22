<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 22/07/16
 * Time: 4:09 PM
 */

require_once 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$dnsResolverFactory = new React\Dns\Resolver\Factory();
$dns = $dnsResolverFactory->createCached('8.8.8.8', $loop);

$tcpConnector = new React\SocketClient\Connector($loop, $dns);

$tcpConnector->create('127.0.0.1', 1337)->then(function (React\Stream\Stream $stream) {
    $stream->on('data', function ($data) {
        $message = unserialize($data);
        var_dump($message->getId());
    });


});

$loop->run();