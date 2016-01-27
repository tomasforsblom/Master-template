Master-base
=========

WHAT IS THIS PROJECT? 
---------------------
A webbtemplate / boilerplate for smaller websites and webbapplications using PHP. It was built by Tomas Forsblom in a course at
Blekinge Tekniska Högskola, read more at http://dbwebb.se/kunskap/anax-en-hallbar-struktur-for-dina-webbapplikationer.

CLASSES
-----------------------------------
In the template you will find classes for:

**CALENDAR**

* CCalendar

**DICE-GAME**

* CDice

* CDiceGame

* CDiceView

**GALLERY**

* CGallery

**SEARCH AND DISPLAY MOVIES**

* CHTMLTable

* CMovieHandler

* CMovieSearch

**CREATE, VIEW, EDIT AND REMOVE USERS**

* CUser

**DISPLAYING THE SOURCE CODE**

* CSource

**USE THE DATABASE**

* CDatabase

**USE TEXTFILTERS**

* CTextfilter

**MODIFY AND USE IMAGES ON THE WEBSITE**

* CImage and the pagecontroller img.php

**INSERT, UPDATE AND REMOVE CONTENT**

* CContent

* CBlog

* CPage

HOW TO USE THE TEMPLATE
-----------------------------------

**MASTER/THEME**

In 'theme' you will find the logic the presents and generates the HTML-page. All content is placed in an array.

**MASTER/SRC**

Here you will find all classes that you will use on the website (see Classes).

**MASTER/WEBROOT**

Here you will find folders for images and stylesheets. You will also find the config-file. This is where your pagecontrollers will be.

License 
------------------

This software is free software and carries a MIT license.


Use of external libraries
-----------------------------------

The following external modules are included and subject to its own license.

### Modernizr
* Website: http://modernizr.com/
* Version: 2.6.2
* License: MIT license 
* Path: included in `webroot/js/modernizr.js`



History
-----------------------------------

v1.0.0 (2016-01-21)

* First release.

------------------


Copyright (c) 2015 Tomas Forsblom
