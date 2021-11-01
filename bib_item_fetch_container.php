<?php

require_once('bib_item_container.php');
require_once('bibtexParse/PARSEENTRIES.php');

abstract class bib_item_fetch_container extends bib_item_container {
    const MAX_FETCH = 1;

    private $num_;
    abstract protected function has_next();
    abstract protected function parse_page($out);
    abstract protected function next_url();
    protected function set_num($n) {
        $this->num_ = $n;
    }
    protected function get_num() {
        return $this->num_;
    }
    protected function fetch() {
        $old = -1;
        $nf = 0;
        while ($this->has_next() && $nf < self::MAX_FETCH) {
            $old = $this->count();
            $ch = curl_init();
            $url = $this->next_url();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $items = curl_exec($ch);
            curl_close($ch);
            $this->push_back($items);
            if ($old == $this->count()) break;
            ++$nf;
        }
        if (isset($this->num_) && $this->num_ != $this->count()) {
            // throw count_does_not_match_exception
        }
    }
    private function push_back($items) {
        if (is_array($items)) $str = implode("\n\n",$items);
        else $str = $items;
        $parse = NEW PARSEENTRIES();
        $parse->expandMacro = TRUE;
        $parse->fieldExtract = TRUE;
        $parse->loadBibtexString($str);
        $parse->extractEntries();
        list($preamble, $strings, $entries, $undefinedStrings)
            = $parse->returnArrays();
        foreach ($entries as $e) {
            $b = new bib_item_single($e);
            $this->add($b);
        }
        
    }
}

?>