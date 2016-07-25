<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 25/07/16
 * Time: 7:19 PM
 */

namespace PhpMQ;


use PhpMQ\Repository\Message;
use React;

class Producer
{
    public function postMessage(Message $m)
    {
        $loop = React\EventLoop\Factory::create();

        $dnsResolverFactory = new React\Dns\Resolver\Factory();
        $dns = $dnsResolverFactory->createCached('localhost', $loop);

        $tcpConnector = new React\SocketClient\Connector($loop, $dns);

        $tcpConnector->create('127.0.0.1', 1337)->then(function (React\Stream\Stream $stream) {
            $stream->write($this->protocol->buildProduce($this->id, $this->qname));
        });
    }
}