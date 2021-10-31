<?php

require_once('bibtexParse/PARSEENTRIES.php');

class bib_item {

    private $data_;

    public function __construct($in) {
        $this->set($in);
    }

    protected final function set($in) {
        if (!is_array($in)) { // assume it is RAW data
            $this->data_ = $this->parse($this->raw_);
        } else {
            $this->data_ = $in;
        }
    }

    private function parse($raw) {
        $parse = NEW PARSEENTRIES();
        $parse->expandMacro = TRUE;
        $parse->fieldExtract = FALSE;
        $parse->loadBibtexString($raw);
        $parse->extractEntries();
        list($preamble, $strings, $entries, $undefinedStrings)
            = $parse->returnArrays();
        return $entries[0];
    }

    public function attr($str = null) {
        if ($str === null) return array_keys($this->data_);
        if (isset($this->data_[$str])) return $this->data_[$str];
	return null;
        // throw no_such_attribute_exception($str);
    }

    public function all_attr() {
        return $this->data_;
    }

    public function add($key, $val) {
    	if (!array_key_exists($key,$this->data_)) $this->data_[$key] = $val;
    }

}