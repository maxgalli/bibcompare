<?php

require_once('bib_item.php');

class bib_item_single extends bib_item {

    private $pair_;

    public function __construct($in) {
        $this->pair_ = null;
        $this->set($in);
    }

    public function set_pair(bib_item_pair &$p) {
        $this->pair_ =& $p;
    }

    public function get_pair() {
        return $this->pair_;
    }
    
    public function has_pair() {
        return isset($this->pair_);
    }


}