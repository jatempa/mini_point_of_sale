<?php

/**
 * Created by PhpStorm.
 * User: jorge antonio atempa
 * Date: 05/08/17
 * Time: 11:28 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Note
 *
 * @ORM\Table(name="notes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NoteRepository")
 */
class Note
{
    /**
     * @var int
     *
     * @ORM\Column(name="note_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     *  @ORM\JoinColumn(name="user", referencedColumnName="user_id")
     */
    private $user;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Account")
     *  @ORM\JoinColumn(name="account", referencedColumnName="account_id")
     */
    private $account;

    /**
     * @var int
     *
     * @ORM\Column(name="numberNote", type="integer")
     */
    private $numberNote;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=30)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="checkin", type="datetime", nullable=true)
     */
    private $checkin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="checkout", type="datetime", nullable=true)
     */
    private $checkout;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Note
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set account
     *
     * @param \AppBundle\Entity\Account $account
     * @return Note
     */
    public function setAccount(\AppBundle\Entity\Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \AppBundle\Entity\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set numberNote
     *
     * @param integer $numberNote
     * @return Note
     */
    public function setNumberNote($numberNote)
    {
        $this->numberNote = $numberNote;

        return $this;
    }

    /**
     * Get numberNote
     *
     * @return integer
     */
    public function getNumberNote()
    {
        return $this->numberNote;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return NoteProduct
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set checkin
     *
     * @param \DateTime $checkin
     * @return Note
     */
    public function setCheckin($checkin)
    {
        $this->checkin = $checkin;

        return $this;
    }

    /**
     * Get checkin
     *
     * @return \DateTime
     */
    public function getCheckin()
    {
        return $this->checkin;
    }

    /**
     * Set checkout
     *
     * @param \DateTime $checkout
     * @return NoteProduct
     */
    public function setCheckout($checkout)
    {
        $this->checkout = $checkout;

        return $this;
    }

    /**
     * Get checkout
     *
     * @return \DateTime
     */
    public function getCheckout()
    {
        return $this->checkout;
    }

    public function __toString()
    {
        return $this->getUser() . ' #' . $this->getNumberNote();
    }
}
