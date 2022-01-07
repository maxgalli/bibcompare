<?php

require_once('bib_item_single.php');

abstract class bib_item_container implements Iterator {
    private $item_;
    private $cache_;
    public function add(bib_item $i) {
        $this->cache_ = null;
        $this->item_[] = $i;
    }
    public function count() {
        return count($this->item_);
    }
    public function get($i) {
        if ($i < $this->count()) return $this->item_[$i];
        //throw out_of_range_exception();
    }

    public function get_by_attr($attr,$value) {
        if (!isset($this->cache_[$attr])) {
            $this->build_cache($attr);
        }
        if (isset($this->cache_[$attr][$value])) {
            return $this->cache_[$attr][$value];
        } else {
            return array();
        }
    }

    private function build_cache($attr) {
        $this->cache_[$attr] = array();
        for ($i = 0; $i < $this->count(); ++$i) {
            if ($v = $this->item_[$i]->attr($attr)) {
                if (!isset($this->cache_[$attr][$v])) {
                    $this->cache_[$attr][$v] = array();
                }
                $t =& $this->cache_[$attr][$v];
                $t[] =& $this->item_[$i];
            }
        }
    }

    private $position_ = 0;
    #[\ReturnTypeWillChange]
    function rewind() {
        $this->position_ = 0;
    }
    #[\ReturnTypeWillChange]
    function current() {
        return $this->get($this->position_);
    }
    #[\ReturnTypeWillChange]
    function key() {
        return $this->position_;
    }
    #[\ReturnTypeWillChange]
    function next() {
        ++$this->position_;
    }
    #[\ReturnTypeWillChange]
    function valid() {
        return ($this->position_ < $this->count());
    }


}

?>
