<?php

require_once('bib_item_container.php');
require_once('bibtexParse/PARSEENTRIES.php');

class bib_item_file_container extends bib_item_container {
    public function __construct($file) {
        $parse = NEW PARSEENTRIES();
        $parse->expandMacro = TRUE;
        $parse->fieldExtract = TRUE;
        $parse->openBib($file);
        $parse->extractEntries();
        $parse->closeBib();
        list($preamble, $strings, $entries, $undefinedStrings)
            = $parse->returnArrays();
        foreach ($entries as $e) {
            $b = new bib_item_single($e);
            $this->add($b);
        }
    }
}

?>