<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 25/07/16
 * Time: 11:19 AM
 */

namespace PhpMQ\Protocol;


class Envelope
{
    private $verb;
    private $cid;
    private $qname;
    private $mid;

    public function __construct($string)
    {
        $values = explode(PhpMQP::FIELD_SEPARATOR, $string);

        $this->verb = $values[0];

        switch ($this->verb) {
            case PhpMQP::VERB_HANDSHAKE:
                $this->cid = $values[1];
                $this->qname = $values[2];
                break;
            case PhpMQP::VERB_STILL_ALIVE:
            case PhpMQP::VERB_GET:
            case PhpMQP::VERB_BYE:
                $this->cid = $values[1];
                break;
            case PhpMQP::VERB_SUCCESS:
            case PhpMQP::VERB_FAILURE:
            case PhpMQP::VERB_RETRY:
                $this->cid = $values[1];
                $this->mid = $values[2];
                break;
        }
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
    public function getCid()
    {
        return $this->cid;
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
    public function getMid()
    {
        return $this->mid;
    }

}