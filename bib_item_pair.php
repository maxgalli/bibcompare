<?php

require_once('bib_item_single.php');
require_once('bib_item_matcher.php');

class bib_item_pair {

    private $item1_;
    private $item2_;

    private $quality_;

    public function __construct(bib_item_single $i1, bib_item_single $i2, bib_item_matcher &$m) {
        $this->item1_ =& $i1;
        $this->item2_ =& $i2;
        $m->set_items($this->item1_,$this->item2_);
        $this->quality_ = $m->get_match();
        if ($this->quality_ != bib_item_matcher::MATCH_NONE) {
            $i1->set_pair($this);
            $i2->set_pair($this);
        }
    }

    public function get_quality() {
        return $this->quality_;
    }

    public function get_item($i) {
        if ($i == 1) return $this->item1_;
        if ($i == 2) return $this->item2_;
        return null;
    }


}

?>