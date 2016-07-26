<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 23/07/16
 * Time: 4:19 PM
 */

namespace PhpMQ\Entity;

/**
 * @Entity @Table(name="dead_messages")
 * @HasLifecycleCallbacks
 */
class DeadMessage extends AbstractMessage
{
    /**
     * @ManyToOne(targetEntity="Queue", inversedBy="dead_messages")
     * @var Queue
     */
    protected $queue;

    /**
     * @return Queue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param Queue $queue
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
    }
}