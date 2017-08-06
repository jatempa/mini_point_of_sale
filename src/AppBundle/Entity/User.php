<?php

/**
 * Created by PhpStorm.
 * User: jorge antonio atempa
 * Date: 05/08/17
 * Time: 11:28 PM
 */

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity()
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=60)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstLastName", type="string", length=60)
     */
    private $firstLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="secondLastName", type="string", length=60, nullable=true)
     */
    private $secondLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="cellphoneNumber", type="string", length=30)
     */
    private $cellphoneNumber;

    public function __construct()
    {
        parent::__construct();
        $this->roles = array("ROLE_USER");
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstLastName
     *
     * @param string $firstLastName
     * @return User
     */
    public function setFirstLastName($firstLastName)
    {
        $this->firstLastName = $firstLastName;

        return $this;
    }

    /**
     * Get firstLastName
     *
     * @return string 
     */
    public function getFirstLastName()
    {
        return $this->firstLastName;
    }

    /**
     * Set secondLastName
     *
     * @param string $secondLastName
     * @return User
     */
    public function setSecondLastName($secondLastName)
    {
        $this->secondLastName = $secondLastName;

        return $this;
    }

    /**
     * Get secondLastName
     *
     * @return string 
     */
    public function getSecondLastName()
    {
        return $this->secondLastName;
    }

    /**
     * Set cellphoneNumber
     *
     * @param string $cellphoneNumber
     * @return User
     */
    public function setCellphoneNumber($cellphoneNumber)
    {
        $this->cellphoneNumber = $cellphoneNumber;

        return $this;
    }

    /**
     * Get cellphoneNumber
     *
     * @return string 
     */
    public function getCellphoneNumber()
    {
        return $this->cellphoneNumber;
    }

    public function __toString()
    {
        return $this->getName() . ' ' . $this->getFirstLastName();
    }
}
