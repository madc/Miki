# Miki. Documentation

Miki. is a small [markdown](http://daringfireball.net/projects/markdown/)-based wiki system. Beside the basic markdown specs, it also has full support for the [markdown extra extension](http://michelf.ca/projects/php-markdown/extra/). It is written in PHP using the [Silex](http://silex.sensiolabs.org/) framework as basis. The pages are saved as .md files in the file structure, no Database is needed.


## Installation

1) Clone the repository.
~~~
$ git clone https://github.com/madc/Miki..git
$ cd Miki./
~~~

2) Get [Composer](http://getcomposer.org) and install dependencies.
~~~
$ wget http://getcomposer.org/composer.phar
$ php composer.phar install
~~~

Visit the Miki. startpage in your broser and setup your first page. You probably find it somewhere like <http://localhost/path/to/Miki./web/>

Inside 'src/config.php' you can change the settings as you like.

## Features

### Basic Authentication

Miki. supports a Basic Authentication. Have a look at src/config.php to set it up.
The authentication is based on this [Gist](https://gist.github.com/1740012) by Masao Maeda.

### Pages

The first page of your wiki will be the 'Index'. With the default settings, it can be found in 'data/wiki/index.md'. To create a new page, exit the index page and insert a markdown link. After clicking on the link you will get the mask to create a new page.

**Hint**
>Clicking on the page name in header will list all other pages inside the actual category lad allow easy switching.

**Example**
~~~
[Linkname](pagename)
~~~

### Categories

The pages can also easily be organized in categories. This can be achieved by using a slash inside the page link.

**Example**
~~~
[Linkname w. Category](category/pagename)
~~~

### Hotkeys

The following keyboard shortcuts are available:

* <kbd>Ctrl</kbd> + <kbd>E</kbd> (or <kbd>Cmd</kbd> + <kbd>E</kbd> on MacOS X) to switch to Edit-Mode
* <kbd>Ctrl</kbd> + <kbd>S</kbd> (or <kbd>Cmd</kbd>+<kbd>S</kbd>) to save changes
* <kbd>Alt</kbd> + <kbd>Ctrl</kbd> + <kbd>X</kbd> (or <kbd>Alt</kbd> + <kbd>Cmd</kbd> + <kbd>X</kbd>) to dismiss changes
* <kbd>Alt</kbd> + <kbd>Ctrl</kbd> + <kbd>H</kbd> (or <kbd>Alt</kbd> + <kbd>Cmd</kbd> + <kbd>H</kbd>) to jump to home

### Responsive and theme-able via Twitter Bootstrap

The HTML layout is based on [Bootstrap 3](http://getbootstrap.com/) and a theme css-file can optionally be defined in the settings.
