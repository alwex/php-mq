<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 25/07/16
 * Time: 12:00 PM
 */

namespace PhpMQ;


use Monolog\Logger;
use PhpMQ\Protocol\Envelope;
use PhpMQ\Protocol\PhpMQP;
use PhpMQ\Utility\MessageBuilder;
use React\Socket\Connection;

class Dispatcher
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var PhpMQP
     */
    private $protocol;

    /**
     * Dispatcher constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->protocol = new PhpMQP();
    }

    /**
     * @param $string
     * @param Connection $connection
     * @return string|void
     */
    public function process($string, Connection $connection)
    {
        $envelope = new Envelope($string);
        $response = "";

        switch ($envelope->getVerb()) {

            case PhpMQP::VERB_HANDSHAKE:
                $response = $this->register($envelope, $connection);
                break;

            case PhpMQP::VERB_BYE:
                $response = $this->unregister($envelope, $connection);
                break;

            case PhpMQP::VERB_STILL_ALIVE:
                $response = $this->stillAlive($envelope);
                break;

            case PhpMQP::VERB_GET:
                $response = $this->get($envelope);
                break;

            case PhpMQP::VERB_SUCCESS:
                $response = $this->success($envelope);
                break;

            case PhpMQP::VERB_RETRY:
                $response = $this->retry($envelope);
                break;

            case PhpMQP::VERB_FAILURE:
                $response = $this->failure($envelope);
                break;
        }

        return $response;
    }

    public function register(Envelope $e, Connection $connection)
    {
        Board::get()->register($e->getCid(), $e->getQname(), $connection);
        $this->logger->addInfo(sprintf("consumer %s registered on queue %s", $e->getCid(), $e->getQname()));

        return $this->protocol->buildAck(PhpMQP::VERB_HANDSHAKE);
    }

    public function unregister(Envelope $e, $connection)
    {
        $cid = Board::get()->getConsumerForConnection($connection);
        Board::get()->unregister($e->getCid());
        $this->logger->addInfo(sprintf("consumer %s unregistered", $e->getCid()));

        return $this->protocol->buildAck(PhpMQP::VERB_BYE);
    }

    public function stillAlive(Envelope $e)
    {
        return $this->protocol->buildAck(PhpMQP::VERB_STILL_ALIVE);
    }

    public function get(Envelope $e)
    {
        $message = Broker::get()->getNextMessage($e->getQname());
        $this->logger->addInfo(sprintf("consumer %s is ready", $e->getCid()));
        $this->logger->addInfo(sprintf("send message %s to consumer %s", $message->getId(), $e->getCid()));

        $response = base64_encode(serialize($message)).MessageBuilder::SEPARATOR;

        if (rand(1, 2) % 2 == 0) {
            $response = $this->protocol->buildWait();
        }

        return $response;
    }

    public function success(Envelope $e)
    {
        return $this->protocol->buildAck(PhpMQP::VERB_SUCCESS);
    }

    public function retry(Envelope $e)
    {
        return $this->protocol->buildAck(PhpMQP::VERB_RETRY);
    }

    public function failure(Envelope $e)
    {
        return $this->protocol->buildAck(PhpMQP::VERB_FAILURE);
    }
}