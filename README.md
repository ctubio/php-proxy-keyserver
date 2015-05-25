[![Stable Release](https://img.shields.io/github/release/ctubio/php-proxy-keyserver.svg?label=stable%20release)](https://github.com/ctubio/php-proxy-keyserver/releases/latest)
[![Platform License](https://img.shields.io/badge/platform-unix--like-lightgray.svg)](https://www.gnu.org/)
[![Software License](https://img.shields.io/badge/license-MIT-111111.svg)](LICENSE)

These sources are happy serving public keys at http://pgp.key-server.io.

[![Latest Tag](https://img.shields.io/packagist/vpre/ctubio/php-proxy-keyserver.svg)](https://packagist.org/packages/ctubio/php-proxy-keyserver)
[![Build Status](https://img.shields.io/travis/ctubio/php-proxy-keyserver/master.svg?label=test%20suite)](https://travis-ci.org/ctubio/php-proxy-keyserver)
[![Coverage Status](https://img.shields.io/coveralls/ctubio/php-proxy-keyserver/master.svg?label=code%20coverage)](https://coveralls.io/r/ctubio/php-proxy-keyserver?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5562f9753664660019240200/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5562f9753664660019240200)
[![Open Issues](https://img.shields.io/github/issues/ctubio/php-proxy-keyserver.svg)](https://github.com/ctubio/php-proxy-keyserver/issues)

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
  $ # Just validate if your website can search/retrieve/submit pgp public keys.
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
##### ..my webserver is just a webserver?
the keyserver may be provided by another different server, if that is your case, please edit ```etc/php-proxy-keyserver.ini``` and customize the value of ```hkp_addr``` to match the address of the keyserver.

##### ..i want to make a skin for the community but without run my own keyserver?
feel free to use my keyserver for your development, the address is ```pgp.key-server.io``` (see the answer above).

##### ..my keyserver refuses to work, can i ask you for help?
i will do my best, please open a [new issue](https://github.com/ctubio/php-proxy-keyserver/issues/new), only after reading the [official sks wiki](https://bitbucket.org/skskeyserver/sks-keyserver/wiki/Home).

##### ..my keyserver is not an instance of ```sks```?
the php proxy will work with any other keyserver as long as it is based on the [OpenPGP HTTP Keyserver Protocol (HKP)](http://ietfreport.isoc.org/all-ids/draft-shaw-openpgp-hkp-00.txt).

##### ..i really don't want a keyserver, but a webserver that uses ```gpg``` locally to answer the request?
hey, the other day i found https://github.com/remko/phkp, hope it helps!

##### ..i belive that is redundant to requiere ```git submodule```, ```composer``` and ```make```?
i like them all. You may probably be able to provide the same functionality with just one of them, but that's a boring world.

### Very special thanks to:
- https://keyserver.mattrude.com
- https://pgp.mit.edu
- https://bitbucket.org/skskeyserver/sks-keyserver
- https://github.com/jenssegers/php-proxy
- https://getcomposer.org/
- https://www.gnu.org/software/make/manual/make.html
- https://git-scm.com/book
