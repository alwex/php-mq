<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 21/07/16
 * Time: 6:59 PM
 */


namespace PhpMQ\Repository;

/**
 * @Entity @Table(name="queues")
 * @HasLifecycleCallbacks
 */
class Queue extends AbstractEntity
{

    const Q_TYPE_Q = 'Q';
    const Q_TYPE_TOPIC = 'TOPIC';

    /**
     * @OneToMany(targetEntity="Message", mappedBy="queue")
     * @var Message[]
     */
    protected $messages;

    /**
     * @OneToMany(targetEntity="DeadMessage", mappedBy="queue")
     * @var DeadMessage[]
     */
    protected $deadMessages;

    /**
     * @Column(type="string", name="q_type")
     * @var string
     */
    protected $qType;

    /**
     * @Column(type="string", unique=true, nullable=false)
     * @var string
     */
    protected $name;

    /**
     * @Column(type="datetime", name="last_read_at", nullable=true)
     * @var \DateTime
     */
    protected $lastReadAt;


    /**
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param Message[] $messages
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return string
     */
    public function getQType()
    {
        return $this->qType;
    }

    /**
     * @param string $qType
     */
    public function setQType($qType)
    {
        $this->qType = $qType;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return DeadMessage[]
     */
    public function getDeadMessages()
    {
        return $this->deadMessages;
    }

    /**
     * @param DeadMessage[] $deadMessages
     */
    public function setDeadMessages($deadMessages)
    {
        $this->deadMessages = $deadMessages;
    }

    /**
     * @return \DateTime
     */
    public function getLastReadAt()
    {
        return $this->lastReadAt;
    }

    /**
     * @param \DateTime $lastReadAt
     */
    public function setLastReadAt($lastReadAt)
    {
        $this->lastReadAt = $lastReadAt;
    }

}