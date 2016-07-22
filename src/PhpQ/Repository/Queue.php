<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 21/07/16
 * Time: 6:59 PM
 */


namespace PhpQ\Repository;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="queues")
 */

class Queue
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
     * @OneToMany(targetEntity="Message", mappedBy="queue")
     * @var Message[]
     */
    protected $queue;
}