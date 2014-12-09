<?php

    namespace Bdloc\AppBundle\Service;

    class Slugger {

        private $book;
        private $doctrine;

        public $yo;

        public function __construct($doctrine){
            $this->doctrine = $doctrine;
        }

        public function setBook($book){
            $this->book = $book;
        }

        public function test(){
            die("test");
        }

        public function sluggify($string){
            $slug = strtolower($string);
            return $slug;
        }

    }