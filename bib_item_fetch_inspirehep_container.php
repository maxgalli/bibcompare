<?php

require_once('bib_item_fetch_container.php');

final class bib_item_fetch_inspirehep_container extends bib_item_fetch_container {
    private $query_;
    public function __construct($query) {
        $this->query_ = $query;
        $this->set_num(null);
        $this->fetch();
    }

    protected function has_next() {
        $num = $this->get_num();
        if (!isset($num)) return true;
        return ($this->count() < $num);
    }

    protected function parse_page($out) {
        preg_match("@<strong>([0-9]+)</strong> records found@",$out,$nmatch);
        if (isset($nmatch[1])) {
            $num = intval($nmatch[1]);
        } else {
            $num = null;
        }
        $this->set_num($num);
        $match = array();
        preg_match_all("@<pre>(.*?)</pre>@sm",$out,$match);
        if (isset($match[1])) return $match[1];
        return array();
    }

    protected function next_url() {
        $c = $this->count() + 1;
        $q = $this->query_;
#        $url = "https://old.inspirehep.net/search?ln=en&of=hx&jrec=$c&q=$q";
#        $url = "https://inspirehep.net/api/literature?ln=en&of=hx&jrec=$c&q=$q";
        $url = "https://inspirehep.net/api/literature?sort=mostrecent&q=$q&format=bibtex&size=50";

        return $url;
    }


}

?>