<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 21/07/16
 * Time: 6:58 PM
 */

namespace PhpMQ\Repository;


use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="messages")
 */
class Message
{
    /**
     * @Id @Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @Column(type="datetime", name="created_at")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @Column(type="datetime", name="updated_at")
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @Column(type="integer")
     * @var int
     */
    protected $priority;

    /**
     * @ManyToOne(targetEntity="Queue", inversedBy="messages")
     * @var Queue
     */
    protected $queue;

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}