<?php

require_once('bib_item_matcher.php');

abstract class bib_item_attr_matcher extends bib_item_matcher {

    protected $attr1_;
    protected $attr2_;
    protected $attr_;

    final protected function compare() {
        if (!is_null($this->item1_)) $this->attr1_ = $this->item1_->all_attr();
        else $this->attr1_ = array();
        if (!is_null($this->item2_)) $this->attr2_ = $this->item2_->all_attr();
        else $this->attr2_ = array();
        $this->attr_ =
            array_unique(
              array_merge(
                array_keys($this->attr1_),
                array_keys($this->attr2_)
              )
            );
        return $this->compare_attr();
    }

    abstract protected function compare_attr();

    protected function present($attr, $side = 0) {
        if ($side == 0) {
            return isset($this->attr1_[$attr]) && isset($this->attr2_[$attr]);
        }
        if ($side == 1) return isset($this->attr1_[$attr]);
        if ($side == 2) return isset($this->attr2_[$attr]);
    }

    protected function equal($attr) {
        if (!$this->present($attr)) return false;
        return $this->attr1_[$attr] == $this->attr2_[$attr];
    }

    protected function get($attr,$side) {
        if ($this->present($attr,$side)) {
            if ($side == 1) return $this->attr1_[$attr];
            if ($side == 2) return $this->attr2_[$attr];
        }
        return null;
    }

}


?>

