<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 27/07/16
 * Time: 1:16 AM
 */

namespace PhpMQ\Protocol;


class Packet
{
    const P_DELIMITER = ':';
    const P_VERB_SUCCESS = 'success';
    const P_VERB_RETRY = 'retry';
    const P_VERB_FAILURE = 'failure';
    const P_VERB_POST = 'post';
    const P_VERB_MESSAGE = 'message';
    const P_VERB_HELLO = 'hello';
    const P_END = 'end';

    private $verb;
    private $id;
    private $qname;
    private $data;
    private $priority;

    public static function parse($string)
    {
        $values = explode(self::P_DELIMITER, $string);

        return new self(
            $values[0],
            $values[1],
            $values[2],
            unserialize(base64_decode($values[3])),
            $values[4]
        );
    }

    public function __construct($verb, $id, $qname, $data, $priority)
    {
        $this->verb = $verb;
        $this->id = $id;
        $this->qname = $qname;
        $this->data = serialize($data);
        $this->priority = $priority;
    }

    public function __toString()
    {
        return $this->verb
        .self::P_DELIMITER
        .$this->id
        .self::P_DELIMITER
        .$this->qname
        .self::P_DELIMITER
        .base64_encode($this->data)
        .self::P_DELIMITER
        .$this->priority
        .self::P_DELIMITER
        .self::P_END;
    }

    /**
     * @return mixed
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getQname()
    {
        return $this->qname;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return unserialize($this->data);
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $verb
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $qname
     */
    public function setQname($qname)
    {
        $this->qname = $qname;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

}