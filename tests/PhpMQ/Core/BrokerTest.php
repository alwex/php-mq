<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 23/07/16
 * Time: 3:14 PM
 */

namespace PhpMQ\Core;


use PhpMQ\Configuration;
use PhpMQ\Entity\Message;
use PhpMQ\Entity\Queue;

class BrokerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Broker
     */
    public $broker;
    
    /**
     * @before
     */
    public function setUp()
    {
        $this->broker = new Broker(Configuration::load());
        $this->broker->clearAll();
    }

    public function testBrokerGet()
    {
        $broker = $this->broker;
        $this->assertInstanceOf('PhpMQ\Core\Broker', $broker);
    }

    public function testBrokerGetEntityManager()
    {
        $broker = $this->broker;
        $this->assertInstanceOf('Doctrine\Orm\EntityManager', $broker->getEntityManager());
    }

    public function testBrokerCreateQueue()
    {
        $broker = $this->broker;
        $broker->createQueue('Q1');

        /** @var Queue $queue */
        $queue = $broker->getEntityManager()
            ->getRepository('PhpMQ\Entity\Queue')
            ->findOneBy(array('name' => 'Q1'));

        $this->assertEquals('Q1', $queue->getName());
    }

    public function testBrokerPostMessage()
    {
        $broker = $this->broker;

        $broker->createQueue('Q1');

        $broker->postMessage('Q1', 'some data 1', 1);
        $broker->postMessage('Q1', 'some data 2', 1);
        $broker->postMessage('Q1', 'some data 0', 0);

        /** @var Message $message */
        $message = $broker->getNextMessage('Q1');

        $this->assertEquals(0, $message->getPriority());
    }
}