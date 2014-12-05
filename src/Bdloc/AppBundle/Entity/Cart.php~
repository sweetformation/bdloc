<?php

namespace Bdloc\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bdloc\AppBundle\Entity\CartRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Cart
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=50)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     */
    private $dateModified;

    /**
     *
     *@ORM\ManytoOne(targetEntity="User", inversedBy="carts")
     */
    private $user;

    /**
     *
     *@ORM\OnetoMany(targetEntity="CartItem", mappedBy="cart")
     */
    private $cartItems;


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
     * @param string $status
     * @return Cart
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
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Cart
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return Cart
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime 
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cartItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set user
     *
     * @param \Bdloc\AppBundle\Entity\User $user
     * @return Cart
     */
    public function setUser(\Bdloc\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Bdloc\AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add cartItems
     *
     * @param \Bdloc\AppBundle\Entity\CartItem $cartItems
     * @return Cart
     */
    public function addCartItem(\Bdloc\AppBundle\Entity\CartItem $cartItems)
    {
        $this->cartItems[] = $cartItems;

        return $this;
    }

    /**
     * Remove cartItems
     *
     * @param \Bdloc\AppBundle\Entity\CartItem $cartItems
     */
    public function removeCartItem(\Bdloc\AppBundle\Entity\CartItem $cartItems)
    {
        $this->cartItems->removeElement($cartItems);
    }

    /**
     * Get cartItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCartItems()
    {
        return $this->cartItems;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function beforeInsert() {
        $this->setDateCreated( new \DateTime() );
        $this->setDateModified( new \DateTime() );
    }

    /**
     * @ORM\PreUpdate
     */
    public function beforeEdit() {
        $this->setDateModified( new \DateTime() );
    }
}
