<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 22/07/16
 * Time: 6:59 PM
 */

namespace PhpMQ;


use Doctrine\ORM\EntityManager;
use PhpMQ\Repository\Message;
use PhpMQ\Repository\Queue;

class Broker
{
    /**
     * @var Broker
     */
    private static $instance = null;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @return Broker
     */
    public static function get()
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __construct()
    {
        $configuration = Configuration::load();
        $this->entityManager = $configuration->getEntityManager();
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function clearAll()
    {
        $this->getEntityManager()
            ->createQuery('DELETE PhpMQ\Repository\Message')
            ->execute();

        $this->getEntityManager()
            ->createQuery('DELETE PhpMQ\Repository\DeadMessage')
            ->execute();

        $this->getEntityManager()
            ->createQuery('DELETE PhpMQ\Repository\Queue')
            ->execute();

        $this->getEntityManager()
            ->createQuery('DELETE PhpMQ\Repository\Subscriber')
            ->execute();
    }

    public function postMessage($queueName, $data, $priority)
    {
        $message = new Message();
        $message->setData($data);
        $message->setPriority($priority);

        $queue = $this->getEntityManager()
            ->getRepository('PhpMQ\Repository\Queue')
            ->findOneBy(array('name' => $queueName));

        $message->setQueue($queue);

        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();

    }

    public function createQueue($queueName)
    {
        $queue = new Queue();
        $queue->setName($queueName);
        $queue->setQType(Queue::Q_TYPE_Q);

        $this->getEntityManager()->persist($queue);
        $this->getEntityManager()->flush();
    }

    public function createTopic($queueName)
    {
        $queue = new Queue();
        $queue->setName($queueName);
        $queue->setQType(Queue::Q_TYPE_TOPIC);

        $this->getEntityManager()->persist($queue);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $queueName
     * @return Message
     */
    public function getNextMessage($queueName)
    {
        $queue = $this->getEntityManager()
            ->getRepository('PhpMQ\Repository\Queue')
            ->findOneBy(array('name' => $queueName));

        $queue->setLastReadAt(new \DateTime());
        $this->getEntityManager()
            ->flush($queue);

        $message = $this->getEntityManager()
            ->getRepository('PhpMQ\Repository\Message')
            ->findOneBy(array('queue' => $queue), array('priority' => 'ASC'));

        return $message;
    }
}