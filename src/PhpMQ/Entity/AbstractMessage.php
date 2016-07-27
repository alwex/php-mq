<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 23/07/16
 * Time: 4:24 PM
 */

namespace PhpMQ\Entity;


use Doctrine\ORM\Mapping as ORM;

class AbstractMessage extends AbstractEntity
{
    const STATUS_NEW = 'NEW';
    const STATUS_RETRY = 'RETRY';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_ACK = '';


    /**
     * @Column(type="datetime", name="next_attempt", nullable=true)
     * @var \DateTime
     */
    protected $nextAttempt;

    /**
     * @Column(type="integer", nullable=false)
     * @var int
     */
    protected $priority;

    /**
     * @Column(type="text")
     * @var string
     */
    protected $data;

    /**
     * @Column(type="text")
     * @var string
     */
    protected $status;

    /**
     * @Column(type="integer", nullable=false)
     * @var int
     */
    protected $retryCount = 0;

    /**
     * @PrePersist
     */
    public function create()
    {
        $nextAttemptDate = new \DateTime();
        $nextAttemptDate->sub(new \DateInterval('PT1S'));

        $this->setNextAttempt($nextAttemptDate);
        parent::create();
    }

    /**
     * @param $data mixed
     */
    public function setData($data)
    {
        $this->data = serialize($data);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return unserialize($this->data);
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getRetryCount()
    {
        return $this->retryCount;
    }

    /**
     * @param int $retryCount
     */
    public function setRetryCount($retryCount)
    {
        $this->retryCount = $retryCount;
    }

    /**
     * @return \DateTime
     */
    public function getNextAttempt()
    {
        return $this->nextAttempt;
    }

    /**
     * @param \DateTime $nextAttempt
     */
    public function setNextAttempt($nextAttempt)
    {
        $this->nextAttempt = $nextAttempt;
    }

}