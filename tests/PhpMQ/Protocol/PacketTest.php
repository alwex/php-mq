<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 27/07/16
 * Time: 10:38 AM
 */

namespace PhpMQ\Protocol;


class PacketTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $p = new Packet(Packet::P_VERB_POST, 1, 'Q1', 'some data', 3);
        $p2 = Packet::parse($p->__toString());

        $this->assertEquals($p->getVerb(), $p2->getVerb());
        $this->assertEquals($p->getPriority(), $p2->getPriority());
        $this->assertEquals($p->getQname(), $p2->getQname());
        $this->assertEquals($p->getData(), $p2->getData());
        $this->assertEquals($p->getId(), $p2->getId());

        $this->assertEquals($p->__toString(), $p2->__toString());
    }
}
