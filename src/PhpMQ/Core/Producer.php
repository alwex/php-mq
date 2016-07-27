<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 27/07/16
 * Time: 2:13 PM
 */

namespace PhpMQ\Core;

use PhpMQ\Exception\RuntimeException;
use PhpMQ\Protocol\Packet;
use React;

class Producer
{
    const PORT = 2222;
    const REMOTE = '127.0.0.1';

    public function post($queueName, $data, $priority)
    {
        $p = new Packet(
            Packet::P_VERB_POST,
            spl_object_hash($this),
            $queueName,
            $data,
            $priority
        );

        // init connection
        // and send data to the server
        $loop = React\EventLoop\Factory::create();
        $dnsResolverFactory = new React\Dns\Resolver\Factory();
        $dns = $dnsResolverFactory->createCached('8.8.8.8', $loop);
        $tcpConnector = new React\SocketClient\Connector($loop, $dns);

        $tcpConnector->create(self::REMOTE, self::PORT)->then(function ($connection) use ($p) {
            $connection->write($p->__toString());

            $connection->on('data', function ($data) use ($connection) {
                $response = Packet::parse($data);
                if ($response->getVerb() == Packet::P_VERB_FAILURE) {
                    throw new RuntimeException(sprintf(
                        'Failed to post message %s on queue %s',
                        $response->getData(),
                        $response->getQname()
                        ));
                }
                $connection->close();
            });
        });

        $loop->run();
    }
}