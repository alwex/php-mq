<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 25/07/16
 * Time: 12:18 PM
 */

namespace PhpMQ;


class BoardTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterAndUnregister()
    {
        $connection1 = $this->createMock('React\Socket\Connection');
        $connection2 = $this->createMock('React\Socket\Connection');

        // register one consumer
        Board::get()->register(1, 'Q1', $connection1);
        $expected = [
            1 => [Board::KEY_QNAME => 'Q1'],
        ];
        $this->assertEquals($expected, Board::get()->getConsumers());

        $expectedConnectionsToConsumers = [
            spl_object_hash($connection1) => 1,
        ];
        $this->assertEquals($expectedConnectionsToConsumers, Board::get()->getConnectionsToConsumers());

        $expectedConsumersToConnections = [
            1 => $connection1,
        ];
        $this->assertEquals($expectedConsumersToConnections, Board::get()->getConsumersToConnections());


        // register another consumer
        Board::get()->register(2, 'Q2', $connection2);

        $expected = [
            1 => [Board::KEY_QNAME => 'Q1'],
            2 => [Board::KEY_QNAME => 'Q2'],
        ];
        $this->assertEquals($expected, Board::get()->getConsumers());

        $expectedConnectionsToConsumers = [
            spl_object_hash($connection1) => 1,
            spl_object_hash($connection2) => 2,
        ];

        $this->assertEquals($expectedConnectionsToConsumers, Board::get()->getConnectionsToConsumers());

        $expectedConsumersToConnections = [
            1 => $connection1,
            2 => $connection2,
        ];
        $this->assertEquals($expectedConsumersToConnections, Board::get()->getConsumersToConnections());

        // unregister consumer 1
        Board::get()->unregister(1);
        $expected = [
            2 => [Board::KEY_QNAME => 'Q2'],
        ];
        $this->assertEquals($expected, Board::get()->getConsumers());

        $expectedConnectionsToConsumers = [
            spl_object_hash($connection2) => 2,
        ];
        $this->assertEquals($expectedConnectionsToConsumers, Board::get()->getConnectionsToConsumers());

        $expectedConsumersToConnections = [
            2 => $connection2,
        ];
        $this->assertEquals($expectedConsumersToConnections, Board::get()->getConsumersToConnections());

        // unregister conumer 2
        Board::get()->unregister(2);
        $expected = [];
        $this->assertEquals($expected, Board::get()->getConsumers());

    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage no consumer registered with id
     */
    public function testUnregisterUnexistingConsumer()
    {
        Board::get()->unregister("I see dead people");
    }
}
