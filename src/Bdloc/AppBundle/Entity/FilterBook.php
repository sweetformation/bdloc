<?php

namespace Bdloc\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FilterBook
 *
 */
class FilterBook
{

    private $id;

    private $page;

    private $orderBy;

    private $orderDir;

    private $numPerPage;

    private $keywords;

    private $categories;

    private $availability;

    public function __construct($page = 1, $orderBy = "dateCreated", $orderDir = "desc", $numPerPage = 30, $keywords = "none", $categories = array(), $availability = "all") {
        $this->page = $page;
        $this->orderBy = $orderBy;
        $this->orderDir = $orderDir;
        $this->numPerPage = $numPerPage;
        $this->keywords = $keywords;
        $this->categories = $categories;
        $this->availability = $availability;
    }

    public function getUrlParams() {
        $params = array(
            "page" => $this->getPage(),
            "orderBy" => $this->getOrderBy(),
            "orderDir" => $this->getOrderDir(),
            "numPerPage" => $this->getNumPerPage(),
            "keywords" => $this->getKeywords(),
            "categories" => $this->getCategories(),
            "availability" => $this->getAvailability(),
        );
        return $params;
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
     * Set page
     *
     * @param integer $page
     * @return FilterBook
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return integer 
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set orderBy
     *
     * @param string $orderBy
     * @return FilterBook
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * Get orderBy
     *
     * @return string 
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Set orderDir
     *
     * @param string $orderDir
     * @return FilterBook
     */
    public function setOrderDir($orderDir)
    {
        $this->orderDir = $orderDir;

        return $this;
    }

    /**
     * Get orderDir
     *
     * @return string 
     */
    public function getOrderDir()
    {
        return $this->orderDir;
    }

    /**
     * Set numPerPage
     *
     * @param integer $numPerPage
     * @return FilterBook
     */
    public function setNumPerPage($numPerPage)
    {
        $this->numPerPage = $numPerPage;

        return $this;
    }

    /**
     * Get numPerPage
     *
     * @return integer 
     */
    public function getNumPerPage()
    {
        return $this->numPerPage;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return FilterBook
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set categories
     *
     * @param string $categories
     * @return FilterBook
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get categories
     *
     * @return string 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set availability
     *
     * @param string $availability
     * @return FilterBook
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * Get availability
     *
     * @return string 
     */
    public function getAvailability()
    {
        return $this->availability;
    }
}
