<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 23/07/16
 * Time: 4:40 PM
 */

namespace PhpMQ;

use React;
use PhpMQ;

class BrokerD
{

    public static function run()
    {
        $logger = new \Monolog\Logger('log');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG));


        $loop = React\EventLoop\Factory::create();
        $socket = new React\Socket\Server($loop);

        $workers = new \SplObjectStorage();

        $socket->on('connection', function ($connection) use ($workers, $logger) {
            $logger->addInfo("bienvenu enflure! {$connection->getRemoteAddress()}");

            $workers->attach($connection);

            $connection->on('end', function () use ($connection, $workers, $logger) {
                $logger->addInfo("il est parti l'enculé");
                $workers->detach($connection);
            });
        });


        $loop->addPeriodicTimer(2, function () use ($logger) {
            $kmem = memory_get_usage(true) / 1024;
            $logger->addInfo("Memory: $kmem KiB");
        });

        $timer = $loop->addPeriodicTimer(0.01, function () use ($workers, $logger) {

            /** @var React\Socket\Connection $worker */
            foreach ($workers as $worker) {
                $message = Broker::get()->getNextMessage('Q1');

                $serialized = serialize($message);

                $logger->addInfo(sprintf(
                    "sending message [%s] to [%s] data: %s",
                    $message->getId(),
                    $worker->getRemoteAddress(),
                    serialize($message->getData()))
                );

                /** @var $worker \React\Socket\Connection */
                $worker->write($serialized . "[END]");
            }
        });

        // just run the broker and wait for connections

        echo "the broker is listening on the port 1337\n";

        $socket->listen(1337);
        $loop->run();
    }
}