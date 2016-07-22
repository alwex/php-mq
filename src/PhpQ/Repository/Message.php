<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 21/07/16
 * Time: 6:58 PM
 */

namespace PhpQ\Repository;


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
     * @Column(type="datetime")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $type;

    /**
     * @ManyToOne(targetEntity="Queue", inversedBy="messages")
     * @var Queue
     */
    protected $queue;

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }
}