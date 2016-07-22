<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 22/07/16
 * Time: 4:35 PM
 */


// socket based chat

require __DIR__.'/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

$conns = new \SplObjectStorage();

$socket->on('connection', function ($conn) use ($conns) {
    $conns->attach($conn);

    $conn->on('data', function ($data) use ($conns, $conn) {
        foreach ($conns as $current) {
            if ($conn === $current) {
                continue;
            }

            $current->write($conn->getRemoteAddress().': ');
            $current->write($data);
        }
    });

    $conn->on('end', function () use ($conns, $conn) {
        $conns->detach($conn);
    });
});

$loop->addPeriodicTimer(2, function () use ($conns) {
    $kmem = memory_get_usage(true) / 1024;
    echo "Memory: $kmem KiB\n";

        foreach ($conns as $current) {

            $current->write("retourne travailler enculé\n");
        }

});

echo "Socket server listening on port 4000.\n";
echo "You can connect to it by running: telnet localhost 4000\n";

$socket->listen(4000);
$loop->run();
