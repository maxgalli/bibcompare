<?php

ini_set('max_execution_time', 0); // to run locally

require_once('bib_item_file_container.php');
require_once('bib_item_fetch_inspirehep_container.php');
require_once('bib_item_pair.php');
require_once('bib_item_attr_matcher.php');
require_once('bib_item_printer.php');


class bib_item_my_matcher extends bib_item_attr_matcher {

    private $skip_attr_;

    public function __construct($skip_attr = null) {
        if (is_array($skip_attr)) $this->skip_attr_ = $skip_attr;
        else if (!empty($skip_attr)) $this->skip_attr_ = array($skip_attr);
        else $this->skip_attr_ = array();
    }

    protected function compare_attr() {
        // perfect match (i.e. all attributes match)
        $match = true;
        foreach ($this->attr_ as $a) {
            if (in_array($a,$this->skip_attr_)) continue;
            if ($this->equal($a)) continue;
            $match = false;
            break;
        }
        if ($match) return self::MATCH_PERFECT;

        // ok match (i.e. some key attributes match)
        if ($this->present('doi')) {
            if ($this->equal('doi')) return self::MATCH_OK;
        } else {
            if ($this->present('eprint')) {
                if ($this->equal('eprint')) return self::MATCH_OK;
            }
            if ($this->present('slaccitation')) {
                if ($this->equal('slaccitation')) return self::MATCH_OK;
            }

            if (
              $this->equal('title') &&
              ($this->equal('author') || $this->equal('collaboration')) &&
              $this->equal('year')
            ) return self::MATCH_OK;

            if (
              ($this->equal('author') || $this->equal('collaboration')) &&
              $this->equal('year') &&
              $this->equal('bibtexCitation')
            ) return self::MATCH_OK;
        }

        // fuzzy matching (i.e. normalize title, ...)
        $nt1 = $this->normalize('title',$this->get('title',1));
        $nt2 = $this->normalize('title',$this->get('title',2));        
        if (
          $nt1 == $nt2 &&
          (
            $this->equal('author') || 
            $this->equal('collaboration') || 
            $this->equal('year')
          )
        ) return self::MATCH_FUZZY;

        // not possible do do any matching
        return self::MATCH_NONE;

    }


    private function normalize($what, $in) {
        switch ($what) {
        case 'title':
            $n = $in;
            $n = str_replace(array('$','^+','^-','\to','LEP-2'),array('','+','-','->','LEP II'),$n);
            $n = preg_replace(array('/([0-9]+)-GeV/i','/\\\sqrt{([^}]*)}/i','#([^*])\*\*\(1/2\)#i'),array('\1 GeV','sqrt(\1)','sqrt(\1)'),$n);
            return $n;
        default:
            return null;
        }
    }
                                      
}



// output

echo '<?xml version="1.0" encoding="utf-8" ?>';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="EN" lang="EN">
    <head>
        <title>bibCompare</title>
        <link href="css/design.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="css/custom-theme/jquery-ui-1.9.2.custom.min.css" media="screen" rel="stylesheet" type="text/css" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="imagetoolbar" content="no" />
        <script src="js/jq.js" type="text/javascript" ></script>
        <script src="js/jqui.js" type="text/javascript" ></script>
        <script src="js/bibcompare.js" type="text/javascript" ></script>
    </head>
    <body>
    <h1>&minus; bibCompare v0.6.1</h1>
        <div id="page">

<?php if (!isset($_POST['compare'])) { ?>
                    
                    <form name="bibcompare" method="post" enctype="multipart/form-data">
                      <input type="hidden" name="compare" value="true" />
                    <label for="side1">Side 1 (BiBTeX-File):</label>
                    <input type="file" name="side1" id="side1" /><br />
                    <label for="side2">Side 2 (Search InspireHEP)</label>
                    <input type="text" name="side2" id="side2" value="" /><br />
                    <input type="submit" name="submit" value="compare" />
                    </form>

<?php } else { ?>

<?php


$c1 = new bib_item_file_container($_FILES['side1']['tmp_name']);
$c2 = new bib_item_fetch_inspirehep_container($_POST['side2']);

$skip = array('owner','type','timestamp','__markedentry');
$pair = array('doi','bibtexCitation','slaccitation','eprint','title');

foreach ($pair as $p) {
    foreach ($c1 as $bi) {
        if (($v = $bi->attr($p)) != '') {
            $match = $c2->get_by_attr($p,$v);
            if (count($match) == 1) {
                if ($match[0]->has_pair()) continue;
                $pl = new bib_item_my_matcher($skip);
                $pair = 
                    new bib_item_pair(
                      $bi,$match[0],$pl
                    );
                if ($pair->get_quality() != bib_item_matcher::MATCH_NONE) break;
            }
        }
    }
}

// full match of remaining items

foreach ($c1 as $bi1) {
    if ($bi1->has_pair()) continue;
    foreach ($c2 as $bi2) {
        if ($bi2->has_pair()) continue;
        $pair = new bib_item_pair($bi1,$bi2,new bib_item_my_matcher($skip));
        if ($pair->get_quality() != bib_item_matcher::MATCH_NONE) break;
    }
}


    $r = '';
$n = '<ul>';

$r .= '<div id="tabs-1">';
$i = 0;
foreach ($c1 as $bi) {
    if ($bi->has_pair()) continue;
    $print = new bib_item_printer($bi);
    $r .= '<h3 class="title"><a href="#">' . trim($bi->attr('title'),'{}') . '</a></h3>';
    $r .= '<div>';
    $r .= '<textarea class="side1">' . $print->raw() . '</textarea>';
    $r .= '</div>';
    ++$i;
}
$r .= '</div>';
$n .= '<li><a href="#tabs-1">No Match Side 1 (' . $i . ')</a></li>';

$r .= '<div id="tabs-2">';
$t = '';
$i = 0;
foreach ($c2 as $bi) {
    if ($bi->has_pair()) continue;
    if ($bi->attr('collaboration') == 'CMS' && $bi->attr('journal') == null) continue;
    $bi->add('type','other');
    $bi->add('owner','gd');
    $print = new bib_item_printer($bi);
    $t .= $print->raw() . "\n\n";
    ++$i;
}
$r .= '<h3 class="title"><a href="#">Special Filter (' . $i . ')</h3>';
$r .= '<div><textarea class="both">';
$r .= $t . '</textarea></div>';
$i = 0;
foreach ($c2 as $bi) {
    if ($bi->has_pair()) continue;
    $bi->add('type','other');
    $bi->add('owner','gd');
    $print = new bib_item_printer($bi);
    $r .= '<h3 class="title"><a href="#">' . trim($bi->attr('title'),'{}') . '</a></h3>';
    $r .= '<div>';
    $r .= '<textarea class="side2 side2only">' . $print->raw() . '</textarea>';
    $r .= '</div>';
    ++$i;
}
$r .= '</div>';
$n .= '<li><a href="#tabs-2">No Match Side 2 (' . $i . ')</a></li>';

$r .= '<div id="tabs-3">';
$i = 0;
foreach ($c1 as $bi) {
    if (
      !$bi->has_pair() ||
      $bi->get_pair()->get_quality() != bib_item_matcher::MATCH_PERFECT
    ) continue;
    $print1 = new bib_item_printer($bi);
    $pl = $bi->get_pair()->get_item(2);
    $print2 = new bib_item_printer($pl);
    $r .= '<h3 class="title"><a href="#">' . trim($bi->attr('title'),'{}') . '</a></h3>';
    $r .= '<div class="hidden">';
    $r .= '<textarea class="side1">' . $print1->raw() . '</textarea>';
    $r .= '<textarea class="side2">' . $print2->raw() . '</textarea>';
    $r .= '</div>';
    ++$i;
}
$r .= '</div>';
$n .= '<li><a href="#tabs-3">Perfect Match (' . $i . ')</a></li>';

$r .= '<div id="tabs-4">';
$i = 0;
foreach ($c1 as $bi) {
    if (
      !$bi->has_pair() ||
      $bi->get_pair()->get_quality() != bib_item_matcher::MATCH_OK
    ) continue;
    $print1 = new bib_item_printer($bi);
    $print2 = new bib_item_printer($bi->get_pair()->get_item(2));
    $r .= '<h3 class="title"><a href="#">' . trim($bi->attr('title'),'{}') . '</a></h3>';
    $r .= '<div class="hidden">';
    $r .= '<textarea class="side1">' . $print1->raw() . '</textarea>';
    $r .= '<textarea class="side2">' . $print2->raw() . '</textarea>';
    $r .= '</div>';
    ++$i;
}
$r .= '</div>';
$n .= '<li><a href="#tabs-4">OK Match (' . $i . ')</a></li>';

$r .= '<div id="tabs-5">';
$i = 0;
foreach ($c1 as $bi) {
    if (
      !$bi->has_pair() ||
      $bi->get_pair()->get_quality() != bib_item_matcher::MATCH_FUZZY
    ) continue;
    $print1 = new bib_item_printer($bi);
    $pl = $bi->get_pair()->get_item(2);
    $print2 = new bib_item_printer($pl);
    $r .= '<h3 class="title"><a href="#">' . trim($bi->attr('title'),'{}') . '</a></h3>';
    $r .= '<div class="hidden">';
    $r .= '<textarea class="side1">' . $print1->raw() . '</textarea>';
    $r .= '<textarea class="side2">' . $print2->raw() . '</textarea>';
    $r .= '</div>';
    ++$i;
}
$r .= '</div>';
$n .= '<li><a href="#tabs-5">Fuzzy Match (' . $i . ')</a></li>';


$n .= '</ul>';

echo $n;
echo $r;

/*
$i = 0;
foreach ($c1 as $bi) {
    if ($bi->has_pair()) continue;
//    print_r($bi->all_attr());
    echo '-- ' . $bi->attr('title') . "\n";
    ++$i;
}
echo "found $i UNMATCHED ENTRIES in C1\n";


$i = 0;
foreach ($c2 as $bi) {
    if ($bi->has_pair()) continue;
    echo '-- ' . $bi->attr('title') . "\n";
    ++$i;
}
echo "found $i UNMATCHED ENTRIES in C2\n";
*/


?>
<?php } ?>
</div>
<div style="clear: both;"></div>
</body>
</html>