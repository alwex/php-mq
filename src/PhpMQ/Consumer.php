<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 22/07/16
 * Time: 6:43 PM
 */

namespace PhpMQ;

use PhpMQ\Utility\MessageBuilder;
use React;

class Consumer
{
    /**
     * @var MessageBuilder
     */
    private $messageBuilder;

    public function __construct()
    {
        $this->messageBuilder = new MessageBuilder();

        $loop = React\EventLoop\Factory::create();

        $dnsResolverFactory = new React\Dns\Resolver\Factory();
        $dns = $dnsResolverFactory->createCached('localhost', $loop);

        $tcpConnector = new React\SocketClient\Connector($loop, $dns);

        $tcpConnector->create('127.0.0.1', 1337)->then(function (React\Stream\Stream $stream) {
            $stream->on('data', function ($data) {
                $this->messageBuilder->addData($data);
            });
        });

        $timer = $loop->addPeriodicTimer(0.1, function () {
            if ($this->messageBuilder->hasMessages()) {
                $messages = $this->messageBuilder->getMessages();
                var_dump($messages);
                foreach ($messages as $message) {
                    $this->run(unserialize($message));
                }
                $this->messageBuilder->clearMessages();
            }
        });

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