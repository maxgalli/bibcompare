<?php

require_once('bib_item_fetch_container.php');

final class bib_item_fetch_inspirehep_container extends bib_item_fetch_container {

    public function __construct($query) {
        /* After the changes introduced in inspireHEP (that can be seen here https://github.com/inspirehep/rest-api-doc)
        results are returned divided into pages of a certain size.
        Also, when a URL is passed, results are returned as a JSON file, where the information that we need is contained
        inside the 'link' key: 'next' is the URL to the following page, 'bibtex' is the current URL in the bibtex format,
        which is the one we need to feed to the already existing machinery.
        In this constructor we exploit these properties to construct an array $bibtex_links containing the URLs with bibtex format for 
        all the different pages. We then loop through it and call $fetch for each of them; fetch is defined in bib_item_fetch_container
        and performs the necessary operations.
        */
        $url = "https://inspirehep.net/api/literature?fields=links&sort=mostrecent&q=$query&size=50";
        $links = $this->get_links($url);
        $bibtex_links = array();
        $bibtex_links[] = $links->bibtex;
        $is_there_next = array_key_exists('next', $links);
        while($is_there_next == TRUE) {
            $next_url = $links->next;
            $links = $this->get_links($next_url);
            $bibtex_links[] = $links->bibtex;
            $is_there_next = array_key_exists('next', $links);
        }

        # get bibitems from each page
        foreach($bibtex_links as $bl) {
            $this->fetch($bl);
        }
    }

    function get_links($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json->links;
    }

}

?>