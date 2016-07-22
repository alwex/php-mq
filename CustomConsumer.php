<?php

/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 22/07/16
 * Time: 6:51 PM
 */
class CustomConsumer extends \PhpQ\Consumer {

    protected function process(\PhpQ\Repository\Message $message) {
        echo "enculé";
        $kmem = memory_get_usage(true) / 1024;
        echo("Memory: $kmem KiB\n");

    }
}