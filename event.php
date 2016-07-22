<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 22/07/16
 * Time: 5:27 PM
 */

require_once __DIR__ . '/vendor/autoload.php';

$emitter = new Evenement\EventEmitter();
$emitter->on('buddy', function () {
    echo 'evenement buddy';
});

$emitter->emit('buddy');
$emitter->emit('buddy');
$emitter->emit('buddy');
