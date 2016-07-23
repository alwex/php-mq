<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 21/07/16
 * Time: 6:59 PM
 */


namespace PhpMQ\Repository;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="queues")
 */
class Queue
{

    const Q_TYPE_Q = 'Q';
    const Q_TYPE_TOPIC = 'TOPIC';

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
     * @OneToMany(targetEntity="Message", mappedBy="queue")
     * @var Message[]
     */
    protected $messages;

    /**
     * @Column(type="string", name="q_type")
     * @var string
     */
    protected $qType;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;
}