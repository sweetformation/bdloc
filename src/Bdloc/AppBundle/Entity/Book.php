<?php
namespace Bdloc\AppBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity(repositoryClass="Bdloc\AppBundle\Entity\BookRepository")
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $num;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $publisher;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cover;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $exlibris;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $pages;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $board;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $idbel;

    /**
     * @ORM\Column(type="integer", length=3, nullable=false)
     */
    private $stock;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateModified;

    /**
     * @ORM\ManyToOne(targetEntity="Bdloc\AppBundle\Entity\Author", inversedBy="books_illustrated")
     * @ORM\JoinColumn(name="illustrator", referencedColumnName="id", nullable=false)
     */
    private $illustrator;

    /**
     * @ORM\ManyToOne(targetEntity="Bdloc\AppBundle\Entity\Author", inversedBy="books_scenarized")
     * @ORM\JoinColumn(name="scenarist", referencedColumnName="id")
     */
    private $scenarist;

    /**
     * @ORM\ManyToOne(targetEntity="Bdloc\AppBundle\Entity\Author", inversedBy="books_colored")
     * @ORM\JoinColumn(name="colorist", referencedColumnName="id")
     */
    private $colorist;

    /**
     * @ORM\ManyToOne(targetEntity="Bdloc\AppBundle\Entity\Serie", inversedBy="books")
     * @ORM\JoinColumn(name="serie_id", referencedColumnName="id")
     */
    private $serie;

    /**
     *
     *@ORM\OnetoOne(targetEntity="CartItem", mappedBy="book")
     */
    private $cartItem;


    /**
     * Set id
     *
     * @param integer $id
     * @return Author
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * Set title
     *
     * @param string $title
     * @return Book
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set num
     *
     * @param integer $num
     * @return Book
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return integer 
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set publisher
     *
     * @param string $publisher
     * @return Book
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * Get publisher
     *
     * @return string 
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * Set isbn
     *
     * @param string $isbn
     * @return Book
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Get isbn
     *
     * @return string 
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * Set cover
     *
     * @param string $cover
     * @return Book
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return string 
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Set exlibris
     *
     * @param string $exlibris
     * @return Book
     */
    public function setExlibris($exlibris)
    {
        $this->exlibris = $exlibris;

        return $this;
    }

    /**
     * Get exlibris
     *
     * @return string 
     */
    public function getExlibris()
    {
        return $this->exlibris;
    }

    /**
     * Set pages
     *
     * @param integer $pages
     * @return Book
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get pages
     *
     * @return integer 
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Set board
     *
     * @param string $board
     * @return Book
     */
    public function setBoard($board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get board
     *
     * @return string 
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Set idbel
     *
     * @param integer $idbel
     * @return Book
     */
    public function setIdbel($idbel)
    {
        $this->idbel = $idbel;

        return $this;
    }

    /**
     * Get idbel
     *
     * @return integer 
     */
    public function getIdbel()
    {
        return $this->idbel;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     * @return Book
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer 
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Book
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
     * @return Book
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
     * Set illustrator
     *
     * @param \Bdloc\AppBundle\Entity\Author $illustrator
     * @return Book
     */
    public function setIllustrator(\Bdloc\AppBundle\Entity\Author $illustrator)
    {
        $this->illustrator = $illustrator;

        return $this;
    }

    /**
     * Get illustrator
     *
     * @return \Bdloc\AppBundle\Entity\Author 
     */
    public function getIllustrator()
    {
        return $this->illustrator;
    }

    /**
     * Set scenarist
     *
     * @param \Bdloc\AppBundle\Entity\Author $scenarist
     * @return Book
     */
    public function setScenarist(\Bdloc\AppBundle\Entity\Author $scenarist)
    {
        $this->scenarist = $scenarist;

        return $this;
    }

    /**
     * Get scenarist
     *
     * @return \Bdloc\AppBundle\Entity\Author 
     */
    public function getScenarist()
    {
        return $this->scenarist;
    }

    /**
     * Set colorist
     *
     * @param \Bdloc\AppBundle\Entity\Author $colorist
     * @return Book
     */
    public function setColorist(\Bdloc\AppBundle\Entity\Author $colorist)
    {
        $this->colorist = $colorist;

        return $this;
    }

    /**
     * Get colorist
     *
     * @return \Bdloc\AppBundle\Entity\Author 
     */
    public function getColorist()
    {
        return $this->colorist;
    }

    /**
     * Set serie
     *
     * @param \Bdloc\AppBundle\Entity\Serie $serie
     * @return Book
     */
    public function setSerie(\Bdloc\AppBundle\Entity\Serie $serie = null)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return \Bdloc\AppBundle\Entity\Serie 
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set cartItem
     *
     * @param \Bdloc\AppBundle\Entity\CartItem $cartItem
     * @return Book
     */
    public function setCartItem(\Bdloc\AppBundle\Entity\CartItem $cartItem = null)
    {
        $this->cartItem = $cartItem;

        return $this;
    }

    /**
     * Get cartItem
     *
     * @return \Bdloc\AppBundle\Entity\CartItem 
     */
    public function getCartItem()
    {
        return $this->cartItem;
    }
}
