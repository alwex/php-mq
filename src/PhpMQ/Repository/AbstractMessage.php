<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 23/07/16
 * Time: 4:24 PM
 */

namespace PhpMQ\Repository;


class AbstractMessage extends AbstractEntity
{
    const STATUS_NEW = 'NEW';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_ACK = '';

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
}