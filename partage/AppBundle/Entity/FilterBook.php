<?php

namespace Bdloc\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FilterBook
 *
 */
class FilterBook
{


    /**
     * @var integer
     *
     */
    private $page;

    /**
     * @var integer
     *
     */
    private $numPerPage;

    /**
     * @var string
     *
     */
    private $orderBy;

    /**
     * @var string
     *
     */
    private $orderDir;

    /**
     * @var string
     *
     */
    private $keywords;  

    /**
     * @var array
     *
     */
    private $categories;  


    /**
     * @var mixed
     *
     */
    private $availability;  


    public function __construct($page = 1, $orderBy = "dateCreated", $orderDir = "desc", $numPerPage = 30, $keywords = "none", $categories = "all", $availability = "all"){

        $this->categories = ($categories == "all") ? array() : $categories;
        $this->page = $page;
        $this->orderBy = $orderBy;
        $this->orderDir = $orderDir;
        $this->numPerPage = $numPerPage;
        $this->keywords = $keywords;
        $this->availability = $availability;
    }

    private function getCategoriesToString(){
        return (empty($this->categories)) ? "all" : implode(",", $this->categories);
    }


    public function getUrlParams(){

        $params = array(
            "page"=>$this->getPage(),
            "numPerPage" => $this->getNumPerPage(),
            "keywords" => $this->getKeywords(),
            "orderBy" => $this->getOrderBy(),
            "orderDir" => $this->getOrderDir(),
            "availability" => $this->getAvailability(),
            "categories" => $this->getCategoriesToString()
        );
        return $params;
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
     * Gets the value of categories.
     *
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Sets the value of categories.
     *
     * @param mixed $categories the categories
     *
     * @return self
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Gets the value of availability.
     *
     * @return integer
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * Sets the value of availability.
     *
     * @param integer $availability the availability
     *
     * @return self
     */
    private function setAvailability($availability)
    {
        $this->availability = $availability;

        return $this;
    }

}
