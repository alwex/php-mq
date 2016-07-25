<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 25/07/16
 * Time: 11:14 AM
 */

namespace PhpMQ\Protocol;


class PhpMQPTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildVerbs()
    {
        $protocol= new PhpMQP();

        $this->assertEquals('h:1:Q1', $protocol->buildHandshake(1, 'Q1'));
        $this->assertEquals('a:1', $protocol->buildStillAlive(1));
        $this->assertEquals('g:1', $protocol->buildGet(1));
        $this->assertEquals('s:1:2', $protocol->buildSuccess(1, 2));
        $this->assertEquals('r:1:2', $protocol->buildRetry(1, 2));
        $this->assertEquals('f:1:2', $protocol->buildFailure(1, 2));
    }
}
