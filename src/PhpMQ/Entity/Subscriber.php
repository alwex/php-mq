<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 23/07/16
 * Time: 12:11 PM
 */

namespace PhpMQ\Entity;


/**
 * Class Subscriber
 * @package PhpMQ\Repository
 * @Entity @Table(name="subscribers")
 */
class Subscriber
{
    /**
     * @Id @Column(type="integer")
     * @var integer
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
     * @Column(type="integer", name="process_id")
     * @var integer
     */
    protected $processId;

    /**
     * @ManyToOne(targetEntity="Queue", inversedBy="subscribers")
     * @var Queue
     */
    protected $queue;

}