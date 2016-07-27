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
use PhpMQ\Exception\RuntimeException;

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

    /**
     * @after
     */
    public function tearDown()
    {
        //$this->broker->clearAll();
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

    public function testBrokerPostMessageOnNoQueue()
    {
        $message = $this->broker->postMessage('Q1', 'some data 1', 1);
        $expected = $this->broker->getNextMessage('Q1');

        $this->assertNotNull($message);
        $this->assertNotNull($expected);
        $this->assertEquals($expected, $message);
    }

    public function testBrokerPostMessage()
    {
        $broker = $this->broker;

        $broker->createQueue('Q1');

        $message1 = $broker->postMessage('Q1', 'some data 1', 1);
        $message2 = $broker->postMessage('Q1', 'some data 2', 1);
        $message3 = $broker->postMessage('Q1', 'some data 3', 0);

        /** @var Message $message */
        $message = $broker->getNextMessage('Q1');
        $this->assertEquals($message3, $message);

        $message = $broker->getNextMessage('Q1');
        $this->assertEquals($message1, $message);

        $message = $broker->getNextMessage('Q1');
        $this->assertEquals($message2, $message);
    }

    public function testGetMessageById()
    {
        $this->broker->createQueue('Q1');
        $postedMessage = $this->broker->postMessage('Q1', 'mesage x', 4);

        $message = $this->broker->getMessageById($postedMessage->getId());

        $this->assertEquals($postedMessage, $message);
    }

    public function testPostMessageOnMultipleQueues()
    {
        $this->broker->createQueue('Q1');
        $this->broker->createQueue('Q2');
        $this->broker->createQueue('Q3');

        $m1Q1 = $this->broker->postMessage('Q1', 'some data 1', 1);
        $m2Q1 = $this->broker->postMessage('Q1', 'some data 2', 1);
        $m3Q1 = $this->broker->postMessage('Q1', 'some data 3', 1);
        $m1Q2 = $this->broker->postMessage('Q2', 'some data 4', 1);
        $m2Q2 = $this->broker->postMessage('Q2', 'some data 5', 1);
        $m1Q3 = $this->broker->postMessage('Q3', 'some data 6', 1);
        $m2Q3 = $this->broker->postMessage('Q3', 'some data 7', 1);

        $message = $this->broker->getNextMessage('Q3');
        $this->assertEquals($m1Q3, $message);

        $message = $this->broker->getNextMessage('Q2');
        $this->assertEquals($m1Q2, $message);

        $message = $this->broker->getNextMessage('Q1');
        $this->assertEquals($m1Q1, $message);

        $message = $this->broker->getNextMessage('Q3');
        $this->assertEquals($m2Q3, $message);
    }

    public function testRetryMessage()
    {
        $this->broker->createQueue('Q1');
        $message = $this->broker->postMessage('Q1', 'some data 1', 1);
        $this->broker->setRetry($message->getId(), 1);

        $this->assertEquals(Message::STATUS_RETRY, $message->getStatus());
        $this->assertEquals(1, $message->getRetryCount());

        $this->broker->setRetry($message->getId(), 1);
        $this->assertEquals(2, $message->getRetryCount());
    }
}