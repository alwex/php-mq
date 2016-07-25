<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 25/07/16
 * Time: 10:58 AM
 */

namespace PhpMQ\Protocol;


use PhpMQ\Repository\Message;

class PhpMQP
{
    const VERB_HANDSHAKE = 'h';
    const VERB_BYE = 'b';
    const VERB_GET = 'g';
    const VERB_SUCCESS = 's';
    const VERB_FAILURE = 'f';
    const VERB_RETRY = 'r';
    const VERB_STILL_ALIVE = 'a';
    const VERB_ACK = 'k';
    const VERB_MESSAGE = 'm';
    const VERB_WAIT = 'w';
    const VERB_PRODUCE = 'p';
    const FIELD_SEPARATOR = ':';

    /**
     * @param $id
     * @param $queueName
     * @return string
     */
    public function buildHandshake($id, $queueName)
    {
        return self::VERB_HANDSHAKE.self::FIELD_SEPARATOR.$id.self::FIELD_SEPARATOR.$queueName;
    }

    public function buildBye($id)
    {
        return self::VERB_BYE.self::FIELD_SEPARATOR.$id;
    }

    public function buildGet($id, $queueName)
    {
        return self::VERB_GET.self::FIELD_SEPARATOR.$id.self::FIELD_SEPARATOR.$queueName;
    }

    public function buildSuccess($id, $mid)
    {
        return self::VERB_SUCCESS.self::FIELD_SEPARATOR.$id.self::FIELD_SEPARATOR.$mid;
    }

    public function buildFailure($id, $mid)
    {
        return self::VERB_FAILURE.self::FIELD_SEPARATOR.$id.self::FIELD_SEPARATOR.$mid;
    }

    public function buildRetry($id, $mid)
    {
        return self::VERB_RETRY.self::FIELD_SEPARATOR.$id.self::FIELD_SEPARATOR.$mid;
    }

    public function buildStillAlive($id)
    {
        return self::VERB_STILL_ALIVE.self::FIELD_SEPARATOR.$id;
    }

    public function buildAck($verb)
    {
        return self::VERB_ACK.self::FIELD_SEPARATOR.$verb;
    }

    public function buildWait()
    {
        return self::VERB_WAIT.self::FIELD_SEPARATOR;
    }

    public function buildProduce($qname, $message)
    {
        return self::VERB_PRODUCE.self::FIELD_SEPARATOR.$qname.self::FIELD_SEPARATOR.$message;
    }

    public function getVerb($data)
    {
        $verb = self::VERB_MESSAGE;
        if (strpos($data, self::FIELD_SEPARATOR) !== false) {
            $verbs = explode(PhpMQP::FIELD_SEPARATOR, $data);
            $verb = $verbs[0];
        }

        return $verb;
    }
}