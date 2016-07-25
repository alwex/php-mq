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

    /**
     * @var React\Socket\Connection
     */
    private $connection;

    public function __construct($qname)
    {
        $this->qname = $qname;
        $this->messageBuilder = new MessageBuilder();
        $this->protocol = new PhpMQP();

        $this->id = uniqid();

        $loop = React\EventLoop\Factory::create();

        $dnsResolverFactory = new React\Dns\Resolver\Factory();
        $dns = $dnsResolverFactory->createCached('localhost', $loop);

        $tcpConnector = new React\SocketClient\Connector($loop, $dns);

        $tcpConnector->create('127.0.0.1', 1337)->then(function (React\Stream\Stream $stream) {
            $this->connection = $stream;

            // say hello and declare yourself
            $stream->write($this->protocol->buildHandshake($this->id, $this->qname));

            $stream->on('data', function ($data) use ($stream) {

                echo "received data: " . $data . PHP_EOL;
                // TODO gérer correctement le verbe dans le cas d'un message
                $verb = $this->protocol->getVerb($data);
                switch ($verb) {
                    case PhpMQP::VERB_ACK:
                        $stream->write($this->protocol->buildGet($this->id, $this->qname));
                        break;

                    case PhpMQP::VERB_WAIT:
                        echo "waiting" .PHP_EOL;
                        break;
                    case PhpMQP::VERB_STILL_ALIVE:
                        $stream->write($this->protocol->buildStillAlive($this->id));
                        break;

                    case PhpMQP::VERB_MESSAGE:
                        $this->messageBuilder->addData($data);
                        if ($this->messageBuilder->hasMessages()) {
                            $messages = $this->messageBuilder->getMessages();
                            foreach ($messages as $message) {
                                $this->run(unserialize(base64_decode($message)));
                            }
                            $this->messageBuilder->clearMessages();
                        }
                        break;
                }

            });
        });

        $loop->run();

    }

    private function run(\PhpMQ\Repository\Message $message)
    {
        $result = $this->process($message);
        $this->connection->write($this->protocol->buildSuccess($this->id, $message->getId()));

    }

    protected function process(\PhpMQ\Repository\Message $message)
    {

    }
}