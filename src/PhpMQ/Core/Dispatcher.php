<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 27/07/16
 * Time: 1:44 AM
 */

namespace PhpMQ\Core;


use Monolog\Logger;
use PhpMQ\Configuration;
use PhpMQ\Protocol\Packet;

class Dispatcher
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Broker
     */
    private $broker;

    /**
     * Dispatcher constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->logger = $configuration->getLogger();
        $this->broker = new Broker($configuration);
    }

    /**
     * dispatch and process incoming packets
     *
     * @param Packet $p
     * @return Packet
     */
    public function dispatch(Packet $p)
    {
        // create an empty packet
        // to answer to the caller
        $response = new Packet('', '', '', '', '');

        switch ($p->getVerb()) {
            // post a new message
            // create it in database
            // and return the creation status
            case Packet::P_VERB_POST:

                $message = $this->broker->postMessage(
                    $p->getQname(),
                    $p->getData(),
                    $p->getPriority()
                );

                $response = new Packet(
                    Packet::P_VERB_SUCCESS,
                    $message->getId(),
                    $p->getQname(),
                    '',
                    0
                );
                break;

            // the consumer just connect
            // himself to the broker
            // let's send hime a first
            // message to play with
            case Packet::P_VERB_HELLO:
                $message = $this->broker->getNextMessage($p->getQname());

                if ($message != null) {
                    $response = new Packet(
                        Packet::P_VERB_MESSAGE,
                        $message->getId(),
                        $p->getQname(),
                        $message->getData(),
                        $message->getPriority()
                    );
                } else {
                    $response = null;
                }
                break;

            // the consummer successfully processed
            // the previous message, he need a
            // new one to process
            case Packet::P_VERB_SUCCESS:

                $this->broker->removeMessage($p->getId());

                $message = $this->broker->getNextMessage($p->getQname());

                if ($message != null) {
                    $response = new Packet(
                        Packet::P_VERB_MESSAGE,
                        $message->getId(),
                        $p->getQname(),
                        $message->getData(),
                        $message->getPriority()
                    );
                } else {
                    $response = null;
                }
                break;

            case Packet::P_VERB_RETRY:
                $this->broker->setRetry($p->getId(), 10);

                $message = $this->broker->getNextMessage($p->getQname());

                if ($message != null) {
                    $response = new Packet(
                        Packet::P_VERB_MESSAGE,
                        $message->getId(),
                        $p->getQname(),
                        $message->getData(),
                        $message->getPriority()
                    );
                } else {
                    $response = null;
                }
                break;
            case Packet::P_VERB_FAILURE:
                break;
        }

        return $response;
    }
}