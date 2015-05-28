[![Release](https://img.shields.io/packagist/vpre/ctubio/php-proxy-keyserver.svg?label=release)](https://packagist.org/packages/ctubio/php-proxy-keyserver)
[![Platform License](https://img.shields.io/badge/platform-unix--like-lightgray.svg)](https://www.gnu.org/)
[![Software License](https://img.shields.io/badge/license-MIT-111111.svg)](LICENSE)

These sources are happy serving public keys at https://pgp.key-server.io (check the [pool status](https://sks-keyservers.net/status/)!).

[![Build Status](https://img.shields.io/travis/ctubio/php-proxy-keyserver/master.svg?label=test%20suite)](https://travis-ci.org/ctubio/php-proxy-keyserver)
[![Coverage Status](https://img.shields.io/coveralls/ctubio/php-proxy-keyserver/master.svg?label=code%20coverage)](https://coveralls.io/r/ctubio/php-proxy-keyserver?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5562f9753664660019240200/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5562f9753664660019240200)
[![Open Issues](https://img.shields.io/github/issues/ctubio/php-proxy-keyserver.svg)](https://github.com/ctubio/php-proxy-keyserver/issues)

### Main Features
 * Minimalistic php framework focused to extend the default static web interface of a keyserver.
 * 5 skins ready to use, but you can make your own (with dynamic php blocks or static html files).
 * Optionally auto indent and validation of html pages before output.
 * Preservation of machine readable output compatible with gpg clients.
 * Meaningful error messages while developing skins (logging or display). 
 * Webserver configs ready for apache2 (you may need to mimic pub/.htaccess for your webserver).
 * HKP meets PHP! (fast and lightweight as possible, supporting static skins for historical purposes).

### How to run your own SKS Keyserver with PHP and friends:
```ini
  $ # Check the latest sks version:
  $ curl https://bitbucket.org/skskeyserver/sks-keyserver/raw/default/VERSION
  $ # Check the available sks versions in your sources:
  $ apt-cache policy sks
  $ # Check your current sks version:
  $ sks version
  $ # Decide if you wanna download and compile the latest sks version.

  $ # Check if your keyserver is up and running:
  $ netstat -l | grep hkp
  $ # If you dont see any output, please start the keyserver daemons.

  $ # Check if your webserver is up and running:
  $ netstat -l | grep http
  $ # If you dont see any output, please start the webserver daemon.

  $ # Download and compose the php proxy with the extensible web interface:
  $ cd /var/www
  $ mkdir your.domain.name
  $ cd your.domain.name
  $ composer self-update
  $ composer create-project ctubio/php-proxy-keyserver . --keep-vcs
  $ make config
  $ make help

  $ # ProxyPass doesn't need to be configured because PHP supplies the proxy.
  $ # PHP forwards all external request from the webserver to a local (or remote) keyserver.
  $ # Make your webserver listen to public ip ports 80, 443 and 11371.
  $ # Make your keyserver listen to public ip port 11370 and local ip port 11371.
  $ # Validate if your website can search/retrieve/submit pgp public keys.
  $ # Validate if your keyserver works using the command line tool gpg.
  $ # Please, feel free to extend or customize as you need the web interface!
```

### Troubleshooting

##### Common Installation Problems:
```
-bash: composer: command not found
```
to fix it, see https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx

##### Silly Winny Problems:
```
'make' is not recognized as an internal or external command
```
to fix it, see http://gnuwin32.sourceforge.net/packages/make.htm

### What if..

##### ..i want to make a skin?
run the following command to create a new skin (using ```skin/default``` as a base, or any other), and if you would like to share it, please read the [ANNOUNCEMENT](ANNOUNCEMENT) file:
```
$ cp -r skin/default skin/new-skin
```

##### ..what methods are available in ```skin/*.phtml``` files?
please make use of ```$this``` methods:
```php
# get any value from etc/php-proxy-keyserver.ini
string $this->getConfig(string $option);
# (you can add new options to the config file as you need)
# for example:
echo $this->getConfig('hkp_addr');   # may print 127.0.0.1
echo $this->getConfig('custom_var'); # may print custom_value
```

```php
# get any block form skin/blocks/*
string $this->getBlock(string $block);
# (you can get blocks from any depth in the path)
# for example:
echo $this->getBlock('gnu_inside');       # parse and print skin/block/gnu_inside.phtml
echo $this->getBlock('happy/gnu_inside'); # parse and print skin/block/happy/gnu_inside.phtml
```

```php
# get any page form skin/page/*
string $this->getPage([string $page]);
# (useful in the layout, or to show the faq page in the footer of all pages?)
# for example:
echo $this->getPage();          # parse and print the current page based on the http/s request
echo $this->getPage('index');   # parse and print page/index.phtml
echo $this->getPage('doc/faq'); # parse and print page/doc/faq.phtml
```

##### ..my skin only uses static files?
the ```skin/default``` uses a php layout to build the given page with blocks. But if you would like to use only html files or any other static format, please see the source of [skin/pgpkeyserver-lite](https://github.com/mattrude/pgpkeyserver-lite) for example, or [skin/XHTML+ES](https://github.com/ctubio/sks-keyserver-sampleWeb-XHTML-ES).

##### ..i want to make a skin for the community but without run my own keyserver?
feel free to use my keyserver for your development, the address is ```pgp.key-server.io``` (see the answer below).

##### ..my webserver is just a webserver?
the keyserver may be provided by another different server, if that is your case, please edit ```etc/php-proxy-keyserver.ini``` and customize the value of ```hkp_addr``` to match the address of the keyserver.

##### ..i want to upgrade to a new version of php-proxy-keyserver?
please run the following commands (using v1.2.3 as an example):
```bash
 $ git fetch;           # see the available new versions in the output
 $ git checkout v1.2.3; # upgrade to v1.2.3
```
or you can revert back to a previous version with:
```bash
 $ git checkout v1.2.2; # downgrade back to v1.2.2
```

##### ..my keyserver is not an instance of ```sks```?
the php proxy will work with any keyserver as long as it is based on the [OpenPGP HTTP Keyserver Protocol (HKP)](http://ietfreport.isoc.org/all-ids/draft-shaw-openpgp-hkp-00.txt).

##### ..i really don't want a keyserver, but a webserver that uses ```gpg``` locally to answer the request?
hey, the other day i found https://github.com/remko/phkp, hope it helps!

### Very special thanks to:
- https://keyserver.mattrude.com
- https://pgp.mit.edu
- https://sks-keyservers.net
- https://bitbucket.org/skskeyserver/sks-keyserver
- https://github.com/jenssegers/php-proxy
- https://getcomposer.org/
- https://www.gnu.org/software/make/manual/make.html
- https://git-scm.com/book
