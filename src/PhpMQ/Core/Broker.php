<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 22/07/16
 * Time: 6:59 PM
 */

namespace PhpMQ\Core;


use Cmyker\DoctrineSqlLogger\Logger;
use Doctrine\ORM\EntityManager;
use PhpMQ\Configuration;
use PhpMQ\Entity\Message;
use PhpMQ\Entity\Queue;
use PhpMQ\Exception\RuntimeException;

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
     * important!
     * if the queue does not exists then
     * the queue is created and persisted
     *
     * @param $queueName
     * @return Queue
     */
    public function getQueueByName($queueName)
    {
        $queue = $this->getEntityManager()
            ->getRepository('PhpMQ\Entity\Queue')
            ->findOneBy(array('name' => $queueName));

        if ($queue == null) {
            $queue = $this->createQueue($queueName);
        }

        return $queue;
    }

    /**
     * @param $queueName
     * @param $data
     * @param $priority
     * @return Message
     */
    public function postMessage($queueName, $data, $priority)
    {
        $queue = $this->getQueueByName($queueName);

        $message = new Message();
        $message->setData($data);
        $message->setPriority($priority);
        $message->setStatus(Message::STATUS_NEW);
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
        $queue = $this->getQueueByName($queueName);

        // update last read at to the queue
        $queue->setLastReadAt(new \DateTime());
        $this->getEntityManager()
            ->flush($queue);

        // retrieve next message to process
        // by queue and priority

        $q = $this->getEntityManager()
            ->getRepository('PhpMQ\Entity\Message')
            ->createQueryBuilder('m');

        $q->where($q->expr()->eq('m.queue', ':queue'))
            ->andWhere($q->expr()->in('m.status', [Message::STATUS_NEW, Message::STATUS_RETRY]))
            ->andWhere($q->expr()->lt('m.nextAttempt', ':date_now'))
            ->orderBy('m.priority', 'ASC')
            ->addOrderBy('m.updatedAt', 'ASC')
            ->addOrderBy('m.retryCount', 'ASC')
            ->addOrderBy('m.id', 'ASC')
            ->addOrderBy('m.nextAttempt', 'ASC')

            ->setParameter(':date_now', new \DateTime())
            ->setParameter(':queue', $queue);

        //$connection = $this->getEntityManager()->getConnection();
        //$sqlLogger = new Logger($connection);
        //$connection->getConfiguration()->setSQLLogger($sqlLogger);

        $messages = $q->getQuery()->getResult();
        $message = array_shift($messages);

        //echo $sqlLogger->lastQuery;
        /*
        $message = $this->getEntityManager()
            ->getRepository('PhpMQ\Entity\Message')
            ->findOneBy(
                array(
                    'queue' => $queue,
                    'status' => array(
                        Message::STATUS_NEW,
                        Message::STATUS_RETRY,
                    )
                ),
                array(
                    'priority' => 'ASC',
                    'updatedAt' => 'ASC',
                    'retryCount' => 'ASC',
                    'id' => 'ASC',
                )
            );
        */

        if ($message != null) {

            $message->setStatus(Message::STATUS_PROCESSING);
            $this->getEntityManager()
                ->flush($message);
        }

        return $message;
    }

    /**
     * @param $id
     */
    public function removeMessage($id)
    {
        $message = $this->getMessageById($id);

        $this->getEntityManager()
            ->remove($message);

        $this->getEntityManager()
            ->flush();
    }

    public function setRetry($id, $delay)
    {
        $message = $this->getMessageById($id);

        $message->setStatus(Message::STATUS_RETRY);
        $nextAttemptDate = new \DateTime();
        $nextAttemptDate->add(new \DateInterval('PT'.$delay.'S'));

        $message->setNextAttempt($nextAttemptDate);
        $message->setRetryCount($message->getRetryCount() + 1);

        $this->getEntityManager()
            ->flush();
    }

    /**
     * @param $id
     * @return Message
     */
    public function getMessageById($id)
    {
        $message = $this->getEntityManager()
            ->find('PhpMQ\Entity\Message', $id);

        return $message;
    }
}