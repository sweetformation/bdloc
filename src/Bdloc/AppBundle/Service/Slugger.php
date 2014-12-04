<?php

    namespace Bdloc\AppBundle\Service;

    class Slugger {

        // setter injection
        private $book;

        public function setBook($book) {
            $this->book = $book;
        }

        private $yo;


        public function __construct($yo) {
            $this->yo = $yo;
        }

        public function test() {
            die("test");
        }

        public function sluggify($string) {
            $slug = strtolower($string);
            return $slug;
        }

    }