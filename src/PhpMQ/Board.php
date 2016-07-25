<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 25/07/16
 * Time: 11:59 AM
 */

namespace PhpMQ;


use React\Socket\Connection;

class Board
{

    const KEY_QNAME = 'qname';
    const KEY_PRIORITY = 'priority';
    const KEY_LAST_SEEN = 'last_seen';

    /**
     * @var Board
     */
    private static $instance;

    private $consumers = [];
    private $connectionsToConsumers = [];
    private $consumersToConnections = [];

    private function __construct()
    {
    }

    public static function get()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConsumersToConnections()
    {
        return $this->consumersToConnections;
    }

    public function getConnectionsToConsumers()
    {
        return $this->connectionsToConsumers;
    }

    public function getConsumers()
    {
        return $this->consumers;
    }

    public function getConsumer($cid)
    {
        $consumer = null;
        if (array_key_exists($cid, $this->getConsumers())) {
            $consumer = $this->getConsumers()[$cid];
        }

        return $consumer;
    }

    public function register($cid, $qname, Connection $connection)
    {
        if (!array_key_exists($cid, $this->consumers)) {
            $this->consumers[$cid] = [];
        }

        $this->consumers[$cid][self::KEY_QNAME] = $qname;
        $this->registerConnection($cid, $connection);
    }

    public function registerConnection($cid, Connection $connection)
    {
        $this->connectionsToConsumers[spl_object_hash($connection)] = $cid;
        $this->consumersToConnections[$cid] = $connection;
    }

    public function unregister($cid)
    {
        if (array_key_exists($cid, $this->consumers)) {
            $connection = $this->consumersToConnections[$cid];
            unset($this->consumersToConnections[$cid]);
            unset($this->connectionsToConsumers[spl_object_hash($connection)]);
            unset($this->consumers[$cid]);
        } else {
            throw new \RuntimeException("no consumer registered with id ".$cid);
        }
    }
}