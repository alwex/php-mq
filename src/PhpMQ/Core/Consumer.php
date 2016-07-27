<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 27/07/16
 * Time: 2:13 PM
 */

namespace PhpMQ\Core;

use PhpMQ\Protocol\Packet;
use React;

class Consumer
{
    const PORT = 1111;
    const REMOTE = '127.0.0.1';

    const CODE_SUCCESS = 0;
    const CODE_RETRY = 1;
    const CODE_FAILURE = 2;


    private $queueName;

    private $buffer;

    public function __construct($queueName)
    {
        $this->queueName = $queueName;
        $this->buffer = '';
    }

    public function run()
    {
        // first packet simulate
        // a success to start the
        // exchange loop
        $p = new Packet(
            Packet::P_VERB_HELLO,
            spl_object_hash($this),
            $this->queueName,
            '',
            0
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
                $this->buffer .= $data;
                if (strpos($this->buffer, Packet::P_END) !== false) {
                    $toConsume = $this->buffer;
                    $this->buffer = '';
                    $response = $this->consume($toConsume);
                    $connection->write($response->__toString());
                }
            });
        });

        $loop->run();
    }

    /**
     * @param $data
     * @return Packet
     */
    private function consume($data)
    {
        $packet = Packet::parse($data);

        echo "consume: ".$data.PHP_EOL;

        // do stuff before consuming

        $code = $this->onMessage($packet->getData());

        // do stuff after consuming

        $response = new Packet(
            Packet::P_VERB_SUCCESS,
            $packet->getId(),
            $packet->getQname(),
            '',
            0
        );

        switch ($code) {
            case self::CODE_RETRY:
                $response->setVerb(Packet::P_VERB_RETRY);
                break;
            case self::CODE_FAILURE:
                $response->setVerb(Packet::P_VERB_FAILURE);
                break;
        }

        return $response;
    }

    protected function onMessage($message)
    {
        //return self::CODE_SUCCESS;
        return rand(1, 2) % 2 == 0 ? self::CODE_SUCCESS : self::CODE_RETRY;
    }
}