<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 22/07/16
 * Time: 6:43 PM
 */

namespace PhpMQ;

use PhpQ\Repository\Message;
use React;

class Consumer
{
    private $buffer;

    public function __construct()
    {
        $loop = React\EventLoop\Factory::create();

        $dnsResolverFactory = new React\Dns\Resolver\Factory();
        $dns = $dnsResolverFactory->createCached('localhost', $loop);

        $tcpConnector = new React\SocketClient\Connector($loop, $dns);

        $tcpConnector->create('127.0.0.1', 1337)->then(function (React\Stream\Stream $stream) {
            $stream->on('data', function ($data) use ($stream) {

                echo $data. "\n";
                $buf = explode("NEXTBUDDY", $data);

                var_dump($buf);
                /*
                    $message = unserialize($data);

                    var_dump(get_class($message));

                    $this->run($message);
                */
            });

            $stream->on('full-drain', function () {
                echo "FULL DRAIN\n";
            });

            $stream->on('something', function () {
                echo "fin de fichier gasrs\n";
                exit;
            });
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