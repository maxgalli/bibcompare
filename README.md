# BibCompare

## Prerequisites 

### MacOS

OS: Monterey 12.1

PHP version: 
```
$ php --version
PHP 8.1.1 (cli) (built: Dec 17 2021 22:21:23) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.1.1, Copyright (c) Zend Technologies
    with Zend OPcache v8.1.1, Copyright (c), by Zend Technologies

$ which php
/opt/homebrew/bin/php
```

Installed with: ```brew install php```

## Quick start

Clone:
```
git clone https://github.com/maxgalli/bibcompare.git
cd bitcompare
```

Start local server: 
```
php -S localhost:8888
```

Search in browser:
```
http://localhost:8888/gd.php
```

## Changes for new InspireHEP API

No idea of how the API of InspireHEP used to be, but some instructions for the modern API can be found [here](https://github.com/inspirehep/rest-api-doc).

The main feature that broke the behavior of this package probably consists in the fact that the elements returned when parsing a URL are now organized in **pages** with a size which is 25 (?) by default and can't be higher than 1000. Inside ```bib_item_fetch_inspirehep_container```, the constructor have been changed and now creates an array containing URLs with different pages; we then loop over this array to get the items and perform exactly the same operations that were performed before. Methods and attributes that are not needed anymore were removed.

Perorming a match with a query like ```dissertori``` still takes around ~3 minutes, probably this can be taken down by applying some more metadata filtering in the URL itself.

## Debug Tips

To avoid running over all Guenther's publications on Inspire (which are a lot and take some time to be fetched) it is convenient to comment out the while loop inside ```bib_item_fetch_inspirehep_container.php```, in order to fetch only the first page of results.

Tests are located in the directory.
