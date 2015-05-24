[![Stable Release](https://img.shields.io/github/release/ctubio/php-proxy-keyserver.svg?label=stable%20release)](https://github.com/ctubio/php-proxy-keyserver/releases/latest)
[![Platform License](https://img.shields.io/badge/platform-unix--like-lightgray.svg)](https://www.gnu.org/)
[![Software License](https://img.shields.io/badge/license-MIT-111111.svg)](LICENSE)

These sources are happy serving public keys at http://pgp.key-server.io

[![Latest Tag](https://img.shields.io/github/tag/ctubio/php-proxy-keyserver.svg?label=latest%20tag)](https://github.com/ctubio/php-proxy-keyserver/tags)
[![Build Status](https://img.shields.io/travis/ctubio/php-proxy-keyserver/master.svg?label=test%20suite)](https://travis-ci.org/ctubio/php-proxy-keyserver)
[![Coverage Status](https://img.shields.io/coveralls/ctubio/php-proxy-keyserver/master.svg?label=code%20coverage)](https://coveralls.io/r/ctubio/php-proxy-keyserver?branch=master)
[![Open Issues](https://img.shields.io/github/issues/ctubio/php-proxy-keyserver.svg)](https://github.com/ctubio/php-proxy-keyserver/issues)

### How to run your own SKS Keyserver with PHP and friends:
```bash
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

  $ # Download and compose the php proxy with all web interface skins:
  $ cd /var/www
  $ mkdir your.domain.name
  $ cd your.domain.name
  $ composer create-project ctubio/php-proxy-keyserver . dev-master

  $ # ProxyPass doesn't need to be configured because PHP supplies the proxy.
  $ # Just validate if your website can search/retrieve/submit pgp public keys.
  $ # Please, feel free to extend or customize as you need the web interface!
```
##### Common Installation Problems:
```bash
-bash: composer: command not found
```
to fix it, see https://getcomposer.org/doc/00-intro.md#globally

##### Silly Winny Problems:
```batch
'make' is not recognized as an internal or external command
```
to fix it, see http://gnuwin32.sourceforge.net/packages/make.htm

### Very special thanks to:
- https://keyserver.mattrude.com
- https://pgp.mit.edu
- https://bitbucket.org/skskeyserver/sks-keyserver
- https://github.com/jenssegers/php-proxy
- https://getcomposer.org/
