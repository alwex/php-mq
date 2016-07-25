<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 25/07/16
 * Time: 12:42 PM
 */

namespace PhpMQ;


use Monolog\Logger;
use PhpMQ\Protocol\PhpMQP;
use React\Socket\Connection;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        Board::get(true);
        $connection  = $this->createMock('React\Socket\Connection');

        $protocol = new PhpMQP();
        $logger = $this->createMock('Monolog\Logger');
        $dispatcher = new Dispatcher($logger);
        $dispatcher->process($protocol->buildHandshake(1, 'Q1'), $connection);

        $expected = [
            1 => [Board::KEY_QNAME => 'Q1']
        ];
        $this->assertEquals($expected, Board::get()->getConsumers());
    }

    public function testUnregister()
    {
        Board::get(true);
        $connection  = $this->createMock('React\Socket\Connection');

        $protocol = new PhpMQP();
        $logger = $this->createMock('Monolog\Logger');
        $dispatcher = new Dispatcher($logger);
        $dispatcher->process($protocol->buildHandshake(1, 'Q1'), $connection);
        $dispatcher->process($protocol->buildBye(1), $connection);

        $expected = [];
        $this->assertEquals($expected, Board::get()->getConsumers());
    }
}
