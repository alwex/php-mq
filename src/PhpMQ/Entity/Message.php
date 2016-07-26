<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 21/07/16
 * Time: 6:58 PM
 */

namespace PhpMQ\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity @Table(name="messages")
 * @HasLifecycleCallbacks
 */
class Message extends AbstractMessage
{

    /**
     * @ManyToOne(targetEntity="Queue", inversedBy="messages")
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