<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 22/07/16
 * Time: 6:59 PM
 */

namespace PhpMQ\Core;


use Doctrine\ORM\EntityManager;
use PhpMQ\Configuration;
use PhpMQ\Entity\Message;
use PhpMQ\Entity\Queue;

class Broker
{
    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Broker constructor.
     */
    public function __construct(Configuration $configuration)
    {
        $this->entityManager = $configuration->getEntityManager();
        $this->logger = $configuration->getLogger();
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function clearAll()
    {
        $this->getEntityManager()
            ->createQuery('DELETE PhpMQ\Entity\Message')
            ->execute();

        $this->getEntityManager()
            ->createQuery('DELETE PhpMQ\Entity\DeadMessage')
            ->execute();

        $this->getEntityManager()
            ->createQuery('DELETE PhpMQ\Entity\Queue')
            ->execute();

        $this->getEntityManager()
            ->createQuery('DELETE PhpMQ\Entity\Subscriber')
            ->execute();
    }

    /**
     * @param $queueName
     * @param $data
     * @param $priority
     * @return Message
     */
    public function postMessage($queueName, $data, $priority)
    {
        $message = new Message();
        $message->setData($data);
        $message->setPriority($priority);
        $message->setStatus(Message::STATUS_NEW);

        $queue = $this->getEntityManager()
            ->getRepository('PhpMQ\Entity\Queue')
            ->findOneBy(array('name' => $queueName));

        $message->setQueue($queue);

        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();

        return $message;
    }

    /**
     * @param $queueName
     * @return Queue
     */
    public function createQueue($queueName)
    {
        $queue = new Queue();
        $queue->setName($queueName);
        $queue->setQType(Queue::Q_TYPE_Q);

        $this->getEntityManager()->persist($queue);
        $this->getEntityManager()->flush();

        return $queue;
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
        // retrieve the queue by name
        $queue = $this->getEntityManager()
            ->getRepository('PhpMQ\Entity\Queue')
            ->findOneBy(array('name' => $queueName));

        // update last read at to the queue
        $queue->setLastReadAt(new \DateTime());
        $this->getEntityManager()
            ->flush($queue);

        // retrieve next message to process
        // by queue and priority
        $message = $this->getEntityManager()
            ->getRepository('PhpMQ\Entity\Message')
            ->findOneBy(array('queue' => $queue), array('priority' => 'ASC'));

        $message->setStatus(Message::STATUS_PROCESSING);
        $this->getEntityManager()
            ->flush($message);

        return $message;
    }
}