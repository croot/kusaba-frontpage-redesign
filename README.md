Kusaba X Front Page Redesign
============================

A fully modded Kusaba X front page

I made it few years ago, and now I decide share it due requests.

Changelog
---------
###### v2 - 06/05/2013 ######
* Changed the way that script get the data from database, In some cases the old script executes over than 30 queries to generate the page, the actual will execute only 9.
* Changed the way that "Last posts" and "Popular threads" are compiled, now Its get only one thread/post from each board
* Now using Dwoo for templates, no more PHP and HTML mixed in only one file.
* Due this change all pages and CSS was rewritten

Features
--------

* Custom Rules Page
* Custom FAQ Page
* Custom News Page
* Ban List

Rules, FAQ and News can be edited through Manage using default Kusaba X options to do it.

Install
-------

Upload all files to your chan root's folder

Open: .htaccess

Look for: 

``DirectoryIndex kusaba.php``

And remove these line

Demo
----

[http://xchan.info/](http://xchan.info/)
