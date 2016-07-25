<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 22/07/16
 * Time: 6:43 PM
 */

namespace PhpMQ;

use PhpMQ\Protocol\PhpMQP;
use PhpMQ\Utility\MessageBuilder;
use React;

class Consumer
{
    /**
     * @var MessageBuilder
     */
    private $messageBuilder;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $qname;

    /**
     * @var PhpMQP
     */
    private $protocol;

    public function __construct($qname)
    {
        $this->qname = $qname;
        $this->messageBuilder = new MessageBuilder();
        $this->protocol = new PhpMQP();

        $this->id = uniqid("consumer-", true);

        $loop = React\EventLoop\Factory::create();

        $dnsResolverFactory = new React\Dns\Resolver\Factory();
        $dns = $dnsResolverFactory->createCached('localhost', $loop);

        $tcpConnector = new React\SocketClient\Connector($loop, $dns);

        $tcpConnector->create('127.0.0.1', 1337)->then(function (React\Stream\Stream $stream) {
            $stream->write($this->protocol->buildHandshake($this->id, $this->qname));


            $stream->on('data', function ($data) {
                $this->messageBuilder->addData($data);
                if ($this->messageBuilder->hasMessages()) {
                    $messages = $this->messageBuilder->getMessages();
                    foreach ($messages as $message) {
                        $this->run(unserialize($message));
                    }
                    $this->messageBuilder->clearMessages();
                }
            });
        });

        // say hello and declare yourself
        $loop->run();

    }

    private function run(\PhpMQ\Repository\Message $message)
    {
        $this->process($message);
    }

    protected function process(\PhpMQ\Repository\Message $message)
    {

    }
}