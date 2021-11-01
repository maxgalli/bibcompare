<?php

require_once('bib_item_container.php');
require_once('bibtexParse/PARSEENTRIES.php');

abstract class bib_item_fetch_container extends bib_item_container {
    protected function fetch($bib_link) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$bib_link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $items = curl_exec($ch);
        curl_close($ch);
        $this->push_back($items);
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