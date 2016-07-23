<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 23/07/16
 * Time: 3:14 PM
 */

namespace PhpMQ;


use PhpMQ\Repository\Message;
use PhpMQ\Repository\Queue;

class BrokerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @beforeClass
     */
    public static function beforeClass()
    {
        Broker::get()->clearAll();
    }

    public function testBrokerGet()
    {
        $broker = Broker::get();
        $this->assertInstanceOf('PhpMQ\Broker', $broker);
    }

    public function testBrokerGetEntityManager()
    {
        $broker = Broker::get();
        $this->assertInstanceOf('Doctrine\Orm\EntityManager', $broker->getEntityManager());
    }

    public function testBrokerCreateQueue()
    {
        $broker = Broker::get();
        $broker->createQueue('Q1');

        /** @var Queue $queue */
        $queue = $broker->getEntityManager()
            ->getRepository('PhpMQ\Repository\Queue')
            ->findOneBy(array('name' => 'Q1'));

        $this->assertEquals('Q1', $queue->getName());
    }

    public function testBrokerPostMessage()
    {
        $broker = Broker::get();

        $broker->postMessage('Q1', 'some data 1', 1);
        $broker->postMessage('Q1', 'some data 2', 1);
        $broker->postMessage('Q1', 'some data 0', 0);

        /** @var Message $message */
        $message = $broker->getNextMessage('Q1');

        $this->assertEquals(0, $message->getPriority());
    }
}