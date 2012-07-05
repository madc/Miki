#Miki. Documentation

Miki. is a small [markdown](http://daringfireball.net/projects/markdown/)-based wiki system. It is written in PHP using the little sister of Symfony2, [Silex](http://silex.sensiolabs.org/), as a basis. The Markdown parsing is handled by [dflydev-markdown](https://github.com/dflydev/dflydev-markdown). No Database is needed as the data are stored as .md files.

##Installation

First clone the repository.
~~~
$ git clone https://github.com/madc/Miki..git
$ cd Miki./
~~~

Get [Composer](http://getcomposer.org) and install vendors.
~~~
$ curl -s http://getcomposer.org/installer | php
$ php composer.phar install
~~~

Visit the Miki. startpage in your broser and setup your first page. You probably find it somewhere like <http://localhost/path/to/Miki./web/>

Inside 'src/config.php' you can change the settings as you like.

##Features

###Basic Authentication

Miki. supports a Basic Authentication. Have a look at src/config.php to set it up.
The authentication is based on this [Gist](https://gist.github.com/1740012) by Masao Maeda.

###Pages

The first page of your wiki will be the 'Index'. With the default settings, it can be found in 'data/wiki/index.md'. To create a new page, exit the index page and insert a markdown link. After clicking on the link you will get the mask to create a new page.
**Example**
~~~
[Linkname](pagename)
~~~

###Categories

The pages can also easily be organized in categories. This can be achieved by using a slash inside the page link.
**Example**
~~~
[Linkname w. Category](category/pagename)
~~~

###Hotkeys

The following keyboard shortcuts are available at the moment:

* Ctrl+E (or Cmd+E on MacOS X) to switch to Edit-Mode
* Ctrl+S (or Cmd+S) to save changes
* Alt+Ctrl+X (or Alt+Cmd+X) to dismiss changes

###Theme-able via Twitter Bootstrap

The style is borrowed from Twitter Bootstrap and the used theme is called **Spacelab** and can be found on [Bootswatch](http://bootswatch.com/spacelab/). 