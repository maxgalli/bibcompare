/*
 * Class reproducer, to be run with "php -f class_reproducer.php"
*/
<?php

function get_links($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $json = json_decode(curl_exec($ch));
    curl_close($ch);
    return $json->links;
}

class foo{
    public function __construct() {
        $url = "https://inspirehep.net/api/literature?fields=links&sort=mostrecent&q=dissertori&size=50";
        $links = get_links($url);
        $this->$bibtex_links = array();
        $this->$bibtex_links[] = $links->bibtex;
        $is_there_next = array_key_exists('next', $links);
        while($is_there_next == TRUE) {
            $next_url = $links->next;
            $links = get_links($next_url);
            $this->$bibtex_links[] = $links->bibtex;
            $is_there_next = array_key_exists('next', $links);
            print_r($this->$bibtex_links);
        }
    }
}

$m = new foo();
