<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 27/07/16
 * Time: 10:38 AM
 */

namespace PhpMQ\Core;


use Doctrine\ORM\EntityManager;
use PhpMQ\Configuration;
use PhpMQ\Entity\Message;
use PhpMQ\Protocol\Packet;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var Broker
     */
    private $broker;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @before
     */
    public function setUp()
    {
        $configuration = Configuration::load();
        $this->entityManager = $configuration->getEntityManager();
        $this->dispatcher = new Dispatcher($configuration);
        $this->broker = new Broker($configuration);

        $this->broker->clearAll();
    }

    /**
     * @after
     */
    public function tearDown()
    {
        //$this->broker->clearAll();
    }

    public function testDispatchPostMessage()
    {
        $this->broker->createQueue('Q1');

        $p = new Packet(
            Packet::P_VERB_POST,
            1,
            'Q1',
            'message 1',
            3
        );

        $r = $this->dispatcher->dispatch($p);

        $expected = new Packet(
            Packet::P_VERB_SUCCESS,
            $r->getId(),
            'Q1',
            '',
            0
        );

        $this->assertEquals($expected, $r);

        $message = $this->broker->getMessageById($r->getId());

        $this->assertEquals($p->getPriority(), $message->getPriority());
        $this->assertEquals($p->getData(), $message->getData());
    }

    public function testDispatchSuccessMessage()
    {

        $this->broker->createQueue('Q1');

        $p1 = new Packet(Packet::P_VERB_POST, 'C1', 'Q1', 'message 1', 1);
        $r1 = $this->dispatcher->dispatch($p1);

        $p2 = new Packet(Packet::P_VERB_POST, 'C1', 'Q1', 'message 2', 0);
        $r2 = $this->dispatcher->dispatch($p2);

        $p5 = new Packet(Packet::P_VERB_POST, 'C1', 'Q1', 'message 3', 0);
        $r5 = $this->dispatcher->dispatch($p5);

        $p3 = new Packet(Packet::P_VERB_SUCCESS, $r1->getId(), 'Q1', '', 0);
        $r3 = $this->dispatcher->dispatch($p3);

        $expected = new Packet(
            Packet::P_VERB_MESSAGE,
            $r2->getId(),
            $p2->getQname(),
            $p2->getData(),
            $p2->getPriority()
        );

        $this->assertEquals($expected, $r3);

        $p4 = new Packet(Packet::P_VERB_SUCCESS, $r2->getId(), 'Q1', '', 0);
        $r4 = $this->dispatcher->dispatch($p4);

        $expected = new Packet(
            Packet::P_VERB_MESSAGE,
            $r5->getId(),
            $p5->getQname(),
            $p5->getData(),
            $p5->getPriority()
        );

        $this->assertEquals($expected, $r4);
    }

    public function testDispatchRetry()
    {
        $this->broker->createQueue('Q1');

        $p1 = new Packet(Packet::P_VERB_POST, 'C1', 'Q1', 'message 1', 1);
        $r1 = $this->dispatcher->dispatch($p1);

        $p3 = new Packet(Packet::P_VERB_POST, 'C1', 'Q1', 'message 2', 1);
        $r3 = $this->dispatcher->dispatch($p3);

        $p2 = new Packet(Packet::P_VERB_RETRY, $r1->getId(), 'Q1', '', 1);
        $r2 = $this->dispatcher->dispatch($p2);

        $message = $this->broker->getMessageById($r1->getId());

        $this->assertEquals(Message::STATUS_RETRY, $message->getStatus());
        $this->assertEquals(1, $message->getRetryCount());

        $expected = new Packet(
            Packet::P_VERB_MESSAGE,
            $r3->getId(),
            $r3->getQname(),
            $p3->getData(),
            $p3->getPriority()
        );

        $this->assertEquals($expected, $r2);


        //sleep(20);
        // TODO find a way to force the milliseconds on the database
/*
        $p4 = new Packet(Packet::P_VERB_SUCCESS, $r3->getId(), 'Q1', '', 0);
        $r4 = $this->dispatcher->dispatch($p4);

        $expected = new Packet(
            Packet::P_VERB_MESSAGE,
            $r1->getId(),
            $r1->getQname(),
            $p1->getData(),
            $p1->getPriority()
        );

        $this->assertEquals($expected, $r4);
*/
    }
}
