# BibCompare

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