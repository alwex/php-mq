<?php

/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 22/07/16
 * Time: 6:51 PM
 */
class CustomConsumer extends \PhpMQ\Consumer {

    protected function process(\PhpMQ\Repository\Message $message) {
        /*
        $kmem = memory_get_usage(true) / 1024;
        echo("Memory: $kmem KiB\n");
        */
        var_dump($message->getData() . md5(rand(10, 100)));
        usleep(rand(100000, 1000000));
    }
}