<?php

$q = "dissertori";

$ch = curl_init();

/*
curl_setopt($ch, CURLOPT_URL,"https://inspirehep.net/literature?ln=en&of=hx&jrec=1&p=Dissertori");
curl_setopt($ch, CURLOPT_URL,"https://old.inspirehep.net/search?ln=en&of=hx&jrec=1&p=Dissertori");


curl_setopt($ch, CURLOPT_URL,"https://inspirehep.net/api/literature?ln=en&of=hx&jrec=1&q=Dissertori");
*/

curl_setopt($ch, CURLOPT_URL,"https://inspirehep.net/api/literature?sort=mostrecent&q=Dissertori&format=bibtex&size=50");



curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$out = curl_exec($ch);
curl_close($ch);

print_r($out);

preg_match("@<strong>([0-9]+)</strong> records found@",$out,$match);
$num = intval($match[1]);

//var_dump($num);

preg_match_all("@<pre>(.*?)</pre>@sm",$out,$match);

print_r($match);

?>