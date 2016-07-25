<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 25/07/16
 * Time: 11:33 AM
 */

namespace PhpMQ\Protocol;


class EnvelopTest extends \PHPUnit_Framework_TestCase
{
    public function testProtocolParsing()
    {
        $protocol = new PhpMQP();
        $string = $protocol->buildHandshake(1, 'Q1');
        $envelope = new Envelope($string);

        $this->assertEquals(PhpMQP::VERB_HANDSHAKE, $envelope->getVerb());
        $this->assertEquals(1, $envelope->getCid());
        $this->assertEquals('Q1', $envelope->getQname());

        $string = $protocol->buildStillAlive(1);
        $envelope = new Envelope($string);

        $this->assertEquals(PhpMQP::VERB_STILL_ALIVE, $envelope->getVerb());
        $this->assertEquals(1, $envelope->getCid());

        $string = $protocol->buildGet(1, 'Q1');
        $envelope = new Envelope($string);

        $this->assertEquals(PhpMQP::VERB_GET, $envelope->getVerb());
        $this->assertEquals(1, $envelope->getCid());
        $this->assertEquals('Q1', $envelope->getQname());

        $string = $protocol->buildSuccess(1, 2);
        $envelope = new Envelope($string);

        $this->assertEquals(PhpMQP::VERB_SUCCESS, $envelope->getVerb());
        $this->assertEquals(1, $envelope->getCid());
        $this->assertEquals(2, $envelope->getMid());

        $string = $protocol->buildFailure(1, 2);
        $envelope = new Envelope($string);

        $this->assertEquals(PhpMQP::VERB_FAILURE, $envelope->getVerb());
        $this->assertEquals(1, $envelope->getCid());
        $this->assertEquals(2, $envelope->getMid());

        $string = $protocol->buildRetry(1, 2);
        $envelope = new Envelope($string);

        $this->assertEquals(PhpMQP::VERB_RETRY, $envelope->getVerb());
        $this->assertEquals(1, $envelope->getCid());
        $this->assertEquals(2, $envelope->getMid());

        $string = $protocol->buildBye(1);
        $envelope = new Envelope($string);

        $this->assertEquals(PhpMQP::VERB_BYE, $envelope->getVerb());
        $this->assertEquals(1, $envelope->getCid());
    }
}
