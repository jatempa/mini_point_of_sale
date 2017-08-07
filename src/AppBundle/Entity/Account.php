<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Account
 *
 * @ORM\Table(name="accounts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountRepository")
 */
class Account
{
    /**
     * @var int
     *
     * @ORM\Column(name="account_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\BarTable")
     *  @ORM\JoinColumn(name="barTable", referencedColumnName="bartable_id")
     */
    private $barTable;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

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
     * Set status
     *
     * @param boolean $status
     * @return Account
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set barTable
     *
     * @param \AppBundle\Entity\BarTable $barTable
     * @return Account
     */
    public function setBarTable(\AppBundle\Entity\BarTable $barTable = null)
    {
        $this->barTable = $barTable;

        return $this;
    }

    /**
     * Get barTable
     *
     * @return \AppBundle\Entity\BarTable 
     */
    public function getBarTable()
    {
        return $this->barTable;
    }

    public function __toString()
    {
        return 'Cuenta ' . $this->getId() . '/ Mesa ' . $this->getBarTable();
    }

}
