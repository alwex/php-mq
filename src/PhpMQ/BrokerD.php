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

    /**
     * @var PhpMQ\Protocol\PhpMQP
     */
    private $protocol;

    public function __construct(\Monolog\Logger $logger)
    {
        $this->logger = $logger;
        $this->dispatcher = new Dispatcher($logger);
        $this->protocol = new PhpMQ\Protocol\PhpMQP();
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

            // Data received
            // data is dispoatched and
            // processed accordingly
            $connection->on('data', function ($data) use ($connection) {
                $this->logger->addDebug("received data: ".$data);
                $response = $this->dispatcher->process($data, $connection);
                $connection->write($response);
            });

            // disconnection of the consumer
            // something is not going as expected
            // check and clean messages if necessary
            // TODO clean messages and backup everything related to the consumer
            $connection->on('end', function ($connection) {
                $consumerId = Board::get()->getConsumerForConnection($connection);
                $this->dispatcher->process($this->protocol->buildBye($consumerId), $connection);
                $this->logger->addInfo(sprintf("consumer %s disconnected", $consumerId));
            });
        });

        $loop->addPeriodicTimer(3, function () {
//            $this->logger->addInfo(var_export(Board::get()->getConsumers(), true));
        });

        // just run the broker and wait for connections

        echo "the broker is listening on the port 1337\n";

        $socket->listen(1337);
        $loop->run();
    }
}