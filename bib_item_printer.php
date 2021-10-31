<?php

require_once('bib_item.php');

class bib_item_printer {

    private $data_;

    public function bib_item_printer(bib_item &$i) {
        $this->data_ = $i->all_attr();
    }
    
    public function raw() {
        if (isset($this->data_['bibtexEntryType'])) {
            $type = $this->data_['bibtexEntryType'];
        } else $type = '';
        if (isset($this->data_['bibtexCitation'])) {
            $cite = $this->data_['bibtexCitation'];
        } else $cite = '';
        $r = '';
        $r .= '@' . strtoupper($type) . '{' . $cite . ',';
        foreach ($this->data_ as $k => $v) {
            if (in_array($k,array('bibtexEntryType','bibtexCitation'))) continue;
            $r .= "\n\t" . $k . ' = {' . $v . '},';
        }
        $r = substr($r,0,-1);
        $r .= "\n" . '}';
        return $r;
    }

}

?>