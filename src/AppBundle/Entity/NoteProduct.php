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
 * NoteProduct
 *
 * @ORM\Table(name="note_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NoteProductRepository")
 */
class NoteProduct
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Note")
     *  @ORM\JoinColumn(name="note", referencedColumnName="note_id")
     */
    private $note;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product")
     *  @ORM\JoinColumn(name="product", referencedColumnName="product_id")
     */
    private $product;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", precision=7, scale=2, options={"default": 0}, nullable=true)
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=30)
     */
    private $status;

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
     * Set amount
     *
     * @param integer $amount
     * @return NoteProduct
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set note
     *
     * @param \AppBundle\Entity\Note $note
     * @return NoteProduct
     */
    public function setNote(\AppBundle\Entity\Note $note = null)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return \AppBundle\Entity\Note 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set product
     *
     * @param \AppBundle\Entity\Product $product
     * @return NoteProduct
     */
    public function setProduct(\AppBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \AppBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set total
     *
     * @param float $total
     * @return NoteProduct
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float 
     */
    public function getTotal()
    {
        return $this->total;
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
}
