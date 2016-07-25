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
use React\Socket\Connection;

class Dispatcher
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function process($string, Connection $connection)
    {
        $envelope = new Envelope($string);
        switch ($envelope->getVerb()) {

            case PhpMQP::VERB_HANDSHAKE:
                $this->register($envelope, $connection);
                break;

            case PhpMQP::VERB_BYE:
                $this->unregister($envelope, $connection);
                break;

            case PhpMQP::VERB_STILL_ALIVE:
                $this->stillAlive($envelope);
                break;

            case PhpMQP::VERB_GET:
                $this->get($envelope);
                break;

            case PhpMQP::VERB_SUCCESS:
                $this->success($envelope);
                break;

            case PhpMQP::VERB_RETRY:
                $this->retry($envelope);
                break;

            case PhpMQP::VERB_FAILURE:
                $this->failure($envelope);
                break;
        }
    }

    public function register(Envelope $e, Connection $connection)
    {
        Board::get()->register($e->getCid(), $e->getQname(), $connection);
        $this->logger->addInfo(sprintf("consumer %s registered on queue %s", $e->getCid(), $e->getQname()));
    }

    public function unregister(Envelope $e, $connection)
    {
        Board::get()->unregister($e->getCid());
        $this->logger->addInfo(sprintf("consumer %s unregistered", $e->getCid()));
    }

    public function stillAlive(Envelope $e)
    {

    }

    public function get(Envelope $e)
    {

    }

    public function success(Envelope $e)
    {

    }

    public function retry(Envelope $e)
    {

    }

    public function failure(Envelope $e)
    {

    }
}