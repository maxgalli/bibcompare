<?php

require_once('bib_item.php');

abstract class bib_item_matcher {

    const MATCH_PERFECT = 0;
    const MATCH_OK      = 1;
    const MATCH_FUZZY   = 2;
    const MATCH_NONE    = 3;

    protected $item1_;
    protected $item2_;

    private $m_;

    final public function set_items(bib_item &$i1, bib_item &$i2) {
        $this->item1_ =& $i1;
        $this->item2_ =& $i2;
        $this->m_ = $this->compare();
    }
    
    abstract protected function compare();

    final public function get_match() {
        return $this->m_;
    }

}

?>