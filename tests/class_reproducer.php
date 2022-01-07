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
    print_r($json->links);
    return $json->links;
}

class foo{
    public function __construct() {
        $url = "https://inspirehep.net/api/literature?fields=links&sort=mostrecent&q=dissertori&size=50";
        $links = get_links($url);
        $bibtex_links = array();
        $bibtex_links[] = $links->bibtex;
        $is_there_next = property_exists($links, 'next');
        while($is_there_next == TRUE) {
            $next_url = $links->next;
            $links = get_links($next_url);
            $bibtex_links[] = $links->bibtex;
            $is_there_next = property_exists($links, 'next');
            print_r($bibtex_links);
        }
    }
}

$m = new foo();
