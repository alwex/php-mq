<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 23/07/16
 * Time: 4:40 PM
 */

namespace PhpMQ;

use Monolog\Logger;
use React;
use PhpMQ;

class BrokerD
{
    private $dispatcher;
    /**
     * @var Logger
     */
    private $logger;

    public function __construct()
    {

        $logger = new \Monolog\Logger('log');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG));

        $this->logger = $logger;
        $this->dispatcher = new Dispatcher($logger);
    }

    public function run()
    {
        $loop = React\EventLoop\Factory::create();
        $socket = new React\Socket\Server($loop);

        // accept connections
        // and deal with consumer messages
        $socket->on('connection', function ($connection) {
            /** @var React\Socket\Connection $connection */
            $this->logger->addInfo("consumer connected from {$connection->getRemoteAddress()}");

            $connection->on('data', function($data) use ($connection) {
                $this->logger->addDebug("received data: " . $data);
                $this->dispatcher->process($data, $connection);
            });

            $connection->on('end', function () use ($connection) {

                $this->logger->addInfo("consumer disconnected");
            });
        });

        /*
        $loop->addPeriodicTimer(1, function() use ($workers, $logger) {
            $message = Broker::get()->getNextMessage('Q1');

            foreach ($workers as $worker) {

                $logger->addInfo(sprintf(
                        "sending message [%s] to [%s] data: %s",
                        $message->getId(),
                        $worker->getRemoteAddress(),
                        serialize($message->getData()))
                );

                $logger->addInfo("un message est envoyé");
                $worker->write(serialize($message));
                $worker->write(PhpMQ\Utility\MessageBuilder::SEPARATOR);
            }
        });
        */

        /*
        $loop->addPeriodicTimer(2, function () use ($logger) {
            $kmem = memory_get_usage(true) / 1024;
            $logger->addInfo("Memory: $kmem KiB");
        });
        */

        // just run the broker and wait for connections

        echo "the broker is listening on the port 1337\n";

        $socket->listen(1337);
        $loop->run();
    }
}