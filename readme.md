#Miki. Documentation

Miki. is a small [markdown](http://daringfireball.net/projects/markdown/)-based wiki system. It is written in PHP using the little sister of Symfony2, [Silex](http://silex.sensiolabs.org/), as a basis. The Markdown parsing is handled by [dflydev-markdown](https://github.com/dflydev/dflydev-markdown). No Database is needed as the data are stored as .md files.

##Installation

The installation progress is described at the official (Silex website)[http://silex.sensiolabs.org/download].
After installing the vendors, just look inside the src/config.php and change what you like.

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

###Theme-able via Twitter Bootstrap

The style is borrowed from Twitter Bootstrap and the used theme is called **Spacelab** and can be found on [Bootswatch](http://bootswatch.com/spacelab/). 