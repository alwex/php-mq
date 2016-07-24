<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 24/07/16
 * Time: 1:28 PM
 */

namespace PhpMQ\Utility;


class MessageBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testAddDataTwoSmallMessages()
    {
        $messageBuilder = new MessageBuilder();
        $data = "lorem ipsum lalala".MessageBuilder::SEPARATOR."something else lalala".MessageBuilder::SEPARATOR;

        $messageBuilder->addData($data);

        $expected = array(
            "lorem ipsum lalala",
            "something else lalala"
        );

        $this->assertEquals($expected, $messageBuilder->getMessages());
        $this->assertEquals('', $messageBuilder->getBuffer());
    }

    public function testAddDataTwoSmallMessageWithNoEndDelimiterThenAnotherPacketWithDelimiter()
    {
        $messageBuilder = new MessageBuilder();
        $data = "lorem ipsum lalala".MessageBuilder::SEPARATOR."something else lalala";

        $messageBuilder->addData($data);

        $expected = array(
            "lorem ipsum lalala"
        );

        $this->assertEquals($expected, $messageBuilder->getMessages());
        $this->assertEquals('something else lalala', $messageBuilder->getBuffer(), "buffer not as expected");

        // second sending of data
        $data = " and another thing loulou".MessageBuilder::SEPARATOR;
        $messageBuilder->addData($data);

        $expected = array(
            "lorem ipsum lalala",
            "something else lalala and another thing loulou"
        );

        $this->assertEquals($expected, $messageBuilder->getMessages());
        $this->assertEquals('', $messageBuilder->getBuffer(), "buffer not as expected");
    }

    public function testAddDataWith3PacketAndNoDelimiters()
    {
        $messageBuilder = new MessageBuilder();
        $data = "data1";

        $messageBuilder->addData($data);

        $expected = array();

        $this->assertEquals($expected, $messageBuilder->getMessages());
        $this->assertEquals('data1', $messageBuilder->getBuffer());

        $data = "data2";

        $messageBuilder->addData($data);

        $expected = array();

        $this->assertEquals($expected, $messageBuilder->getMessages());
        $this->assertEquals('data1data2', $messageBuilder->getBuffer());

        $data = "data3";

        $messageBuilder->addData($data);

        $expected = array();

        $this->assertEquals($expected, $messageBuilder->getMessages());
        $this->assertEquals('data1data2data3', $messageBuilder->getBuffer());

        $data = "data4".MessageBuilder::SEPARATOR;

        $messageBuilder->addData($data);

        $expected = array(
            "data1data2data3data4"
        );

        $this->assertEquals($expected, $messageBuilder->getMessages());
        $this->assertEquals('', $messageBuilder->getBuffer());
    }

    public function testGetFirstPart()
    {
        $messageBuilder = new MessageBuilder();
        $data = "lorem ipsum lalala".MessageBuilder::SEPARATOR."something else lalala".MessageBuilder::SEPARATOR."truc";

        // first attempt
        $parts = $messageBuilder->getFirstPart($data);

        $expected = array(
            MessageBuilder::KEY_PART => "lorem ipsum lalala",
            MessageBuilder::KEY_REMAIN => "something else lalala".MessageBuilder::SEPARATOR."truc"
        );

        $this->assertEquals($expected, $parts);

        // second attempt
        $data = $parts[MessageBuilder::KEY_REMAIN];
        $parts = $messageBuilder->getFirstPart($data);

        $expected = array(
            MessageBuilder::KEY_PART => "something else lalala",
            MessageBuilder::KEY_REMAIN => "truc"
        );

        $this->assertEquals($expected, $parts);
    }
}
