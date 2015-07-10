[![Release](https://img.shields.io/packagist/vpre/ctubio/php-proxy-keyserver.svg?label=release)](https://packagist.org/packages/ctubio/php-proxy-keyserver)
[![Platform License](https://img.shields.io/badge/platform-unix--like-lightgray.svg)](https://www.gnu.org/)
[![Software License](https://img.shields.io/badge/license-MIT-111111.svg)](LICENSE)

These sources are happy serving public keys at https://pgp.key-server.io (check the [pool status](https://sks-keyservers.net/status/)!).

[![Build Status](https://img.shields.io/travis/ctubio/php-proxy-keyserver/master.svg?label=test%20suite)](https://travis-ci.org/ctubio/php-proxy-keyserver)
[![Coverage Status](https://img.shields.io/coveralls/ctubio/php-proxy-keyserver/master.svg?label=code%20coverage)](https://coveralls.io/r/ctubio/php-proxy-keyserver?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/9f6e4b8d-d42a-4c74-9dc5-fba26399c373.svg)](https://insight.sensiolabs.com/projects/9f6e4b8d-d42a-4c74-9dc5-fba26399c373)
[![Dependency Status](https://www.versioneye.com/user/projects/5562f9753664660019240200/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5562f9753664660019240200)
[![Open Issues](https://img.shields.io/github/issues/ctubio/php-proxy-keyserver.svg)](https://github.com/ctubio/php-proxy-keyserver/issues)

### Main Features
 * Minimalistic php framework focused to extend and prettify the default web interface of a keyserver.
 * PHPize any request at any port for humans, but keep the original output for gpg/pool clients.
 * 8 skins (thank you folks!), but you can make your own (with dynamic php blocks or static html).
 * Optionally auto addition and validation of user submitted membership lines for new peers.
 * Optionally auto indent and validation of html pages before output html responses.
 * Meaningful (hope you like stack traces) error messages while developing skins/pages.
 * Webserver configs ready for apache2 or nginx.
 * Load Balancer configs ready for haproxy (between PHP and HKP, or balance PHP too).
 * BOINC Status GUI RPC ready for display current assigned tasks on your server farm.
 * Or trash all *modern* features and stick with the great old plain html frontend (for historical purposes).

### How to run your own SKS Keyserver with PHP and friends:
```ini
  $ # Check the latest sks version:
  $ curl https://bitbucket.org/skskeyserver/sks-keyserver/raw/default/VERSION
  $ # Check the available sks versions in your sources:
  $ apt-cache policy sks
  $ # Check your current sks version:
  $ sks version
  $ # Decide if you wanna download and compile the latest sks version.
  
  $ # The README file have examples of configuration files for apache, haproxy and sks.
  
  $ # Check if your keyserver is up and running (in all machines):
  $ netstat -anp | egrep --color 'sks'
  tcp   0    0 0.0.0.0:11370                 0.0.0.0:*     LISTEN      8198/sks
  tcp   0    0 127.0.0.1:11371               0.0.0.0:*     LISTEN      8197/sks
  tcp6  0    0 :::11370                      :::*          LISTEN      8198/sks
  unix  2    [ ACC ]    STREAM   LISTENING   29826   8197/sks   /var/lib/sks/db_com_sock
  unix  2    [ ACC ]    STREAM   LISTENING   29835   8198/sks   /var/lib/sks/recon_com_sock
  $ # If you don't see any output, please start the keyserver daemons with similar configs.

  $ # Optionally, check if your load balancer is up and running (in primary machine):
  $ netstat -anp | egrep --color 'haproxy'
  tcp   0     0 0.0.0.0:11369                0.0.0.0:*     LISTEN      2438/haproxy
  unix  2     [ ]       DGRAM                11553   2008/rsyslogd  /var/lib/haproxy/dev/log
  unix  2     [ ]       DGRAM                12323   2438/haproxy
  $ # Here port 11369 is used, but you are free to choose any other number if you wish.
  $ # A load balancer isn't mandatory, unless you plan to generate daily keydumps.
  
  $ # Check if your webserver is up and running (in primary machine):
  $ netstat -anp | egrep --color 'apache2|nginx'
  tcp   0     0    10.10.10.2:11371          0.0.0.0:*     LISTEN      3197/apache2
  tcp   0     0    10.10.10.2:80             0.0.0.0:*     LISTEN      3197/apache2
  tcp   0     0    10.10.10.2:443            0.0.0.0:*     LISTEN      3197/apache2
  tcp6  0     0    2607:f298:6050:6f:11371   :::*          LISTEN      9647/apache2
  tcp6  0     0    2607:f298:6050:6f81::80   :::*          LISTEN      9647/apache2
  tcp6  0     0    2607:f298:6050:6f81:443   :::*          LISTEN      9647/apache2
  $ # The 4th column may be your own public IPs of your virtual machine/server.
  $ # If you don't see any output, please start the webserver daemon with similar configs.
  
  $ # Download and compose the php proxy and the extensible web interface between them:
  $ cd /var/www
  $ mkdir your.domain.name
  $ cd your.domain.name
  $ composer self-update
  $ composer create-project ctubio/php-proxy-keyserver . --keep-vcs
  $ make config
  $ make help
  $ # All done, thank you!

  $ # Validate if your website can search/retrieve/submit pgp public keys.
  $ # Validate if your keyserver works using the command line tool gpg (or others).
  $ # Import the most recent database dump, and use the mailing list to find peers.
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
run the following command to create a new skin (using ```skin/default``` as a base, or any other), and if you would like to share it, please read the [CONTRIBUTING](CONTRIBUTING) file:
```
$ cp -r skin/default skin/new-skin
```

##### ..i want documentation about the available methods in ```skin/*.phtml``` files?
Yes Sir/Milady, please make use of ```$this``` 3 built-in methods from any phtml file:
```php
# get any value from etc/php-proxy-keyserver.ini
string $this->getConfig(string $option);
# (you can add new options to the config file as you need)
# for example:
echo $this->getConfig('hkp_load_balanced_addr'); # may print 127.0.0.1
echo $this->getConfig('custom_var');             # may print custom_value
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
# (useful in the layout, or to show some page in the footer of all pages?)
# for example:
echo $this->getPage();            # parse and print the current page based on http request
echo $this->getPage('index');     # parse and print page/index.phtml
echo $this->getPage('path/file'); # parse and print path/file.phtml
```

##### ..i don't want to use php?
the ```skin/default``` uses a php layout to build the given page with blocks. But if you would like to use only html files or any other static format, please see the source of [skin/pgpkeyserver-lite](https://github.com/mattrude/pgpkeyserver-lite) or [skin/XHTML+ES](https://github.com/ctubio/sks-keyserver-sampleWeb-XHTML-ES) as examples.

##### ..i want to make a skin for the community but without run my own keyserver?
feel free to use my keyserver for your development, the address is ```pgp.key-server.io``` (see the answer below).

##### ..my server is just a webserver?
the keyserver may be provided by another different server, if that is your case, please edit ```etc/php-proxy-keyserver.ini``` and customize the value of ```hkp_load_balanced_addr``` to match the address of the keyserver.

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

##### ..i would like to see some sks configs:
please take this as an example:
```
# debuglevel 3 is default (max. debuglevel is 10)
basedir:            /var/lib/sks
debuglevel:         3
hostname:           your.domain.name
nodename:           your.node.name
hkp_port:           11371
hkp_address:        127.0.0.1
recon_port:         11370
#recon_address:     127.0.0.1
#
server_contact:			0xYOUR64BITKEYID
from_addr:			    pgp-public-keys@hostname
sendmail_cmd:		  	/usr/sbin/sendmail -t -oi
initial_stat:
disable_mailsync:
membership_reload_interval: 21
stat_hour:          21
#
# set DB file pagesize as recommended by db_tuner
# pagesize is (n * 512) bytes
# NOTE: These must be set _BEFORE_ [fast]build & pbuild and remain set
# for the life of the database files. To change a value requires recreating
# the database from a dump
#
# KDB/key		65536
pagesize: 1        28
#
# KDB/keyid		     32768
keyid_pagesize:    64
#
# KDB/meta	    	 512
meta_pagesize:     1
# KDB/subkeyid		 65536
subkeyid_pagesize: 128
#
# KDB/time	    	 65536
time_pagesize:     128
#
# KDB/tqueue		   512
tqueue_pagesize:   1
#
# KDB/word - db_tuner suggests 512 bytes. This locked the build process
# Better to use a default of 8 (4096 bytes) for now
word_pagesize:		 8
#
# PTree/ptree		   4096
ptree_pagesize:    8
```

##### ..i would like to see some haproxy configs:
here is a basic setup for a network (see the output of netstat command at the top of the README file) with a single ```apache2``` running a single ```php-proxy-keyserver``` that forwards hkp request to a single ```haproxy``` to balance the load of multiple redundant ```sks``` keyservers (the objective here is to avoid the downtimes while making daily keydumps, additionaly you can put the webserver behind another load balancing setup, ofcourse):
```
global
  log /dev/log local0
  log /dev/log local1 notice
  chroot /var/lib/haproxy
  maxconn 4096
  user  haproxy
  group haproxy
  daemon

defaults
  log     global
  mode    http
  option  httplog
  option  dontlognull
  option  http-server-close
  option  forwardfor
  timeout connect 5000
  timeout client  50000
  timeout server  50000
  retries 2
  option  redispatch
  stats enable
  stats hide-version
  stats uri /haproxy
  errorfile 400 /etc/haproxy/errors/400.http
  errorfile 403 /etc/haproxy/errors/403.http
  errorfile 408 /etc/haproxy/errors/408.http
  errorfile 500 /etc/haproxy/errors/500.http
  errorfile 502 /etc/haproxy/errors/502.http
  errorfile 503 /etc/haproxy/errors/503.http
  errorfile 504 /etc/haproxy/errors/504.http

listen php-proxy-keyserver *:11369
  balance leastconn
  server carles.tubio.sks-database_0 127.0.0.1:11371 check
  server carles.tubio.sks-database_1 10.10.10.21:11371 check
  server carles.tubio.sks-database_2 10.10.10.22:11371 check
  server carles.tubio.sks-database_3 10.10.10.23:11371 check
```

##### ..i would like to see some nginx configs:
please take this files as an examples, where you should replace the keywords ```YOUR.PUBLIC.IPv4```, ```YOUR.PUBLIC.IPv6``` and ```YOUR.DOMAIN.NAME```.

Enable support for standard HKP, HTTP and HTTTPS requests:
```
server {
        listen   YOUR.PUBLIC.IPv4:80;
        listen   [YOUR.PUBLIC.IPv6]:80;
        listen   YOUR.PUBLIC.IPv4:443 ssl;
        listen   [YOUR.PUBLIC.IPv6]:443 ssl;
        server_name www.YOUR.DOMAIN.NAME;
        rewrite ^ $scheme://YOUR.DOMAIN.NAME$uri permanent;
        ssl_certificate /etc/nginx/keys/YOUR.DOMAIN.NAME.crt;
        ssl_certificate_key /etc/nginx/keys/YOUR.DOMAIN.NAME.key;
        ssl_session_timeout 5m;
        ssl_protocols SSLv3 TLSv1;
        ssl_ciphers ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv3:+EXP;
        ssl_prefer_server_ciphers on;
}

server {
        listen   YOUR.PUBLIC.IPv4:80;
        listen   [YOUR.PUBLIC.IPv6]:80;
        listen   YOUR.PUBLIC.IPv4:11371;
        listen   [YOUR.PUBLIC.IPv6]:11371;
        listen   YOUR.PUBLIC.IPv4:443 ssl;
        listen   [YOUR.PUBLIC.IPv6]:443 ssl;

        root /var/www/YOUR.DOMAIN.NAME/pub;
        index php-proxy-keyserver.php;

        disable_symlinks off;

        server_name YOUR.DOMAIN.NAME pool.sks-keyservers.net *.pool.sks-keyservers.net;

        location /dump {
         autoindex on;
         add_before_body /dump/.css;
        }

        location / {
         try_files $uri $uri/ /php-proxy-keyserver.php?$query_string;
        }

        location ~ \.php$ {
         fastcgi_split_path_info ^(.+\.php)(/.+)$;
         fastcgi_pass unix:/var/run/php5-fpm.sock;
         fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
         include fastcgi_params;
        }

        location ~ /\.ht {
         deny all;
        }

        ssl_certificate /etc/nginx/keys/YOUR.DOMAIN.NAME.crt;
        ssl_certificate_key /etc/nginx/keys/YOUR.DOMAIN.NAME.key;
        ssl_session_timeout 5m;
        ssl_protocols SSLv3 TLSv1;
        ssl_ciphers ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv3:+EXP;
        ssl_prefer_server_ciphers on;
}
```
##### ..i would like to see some apache2 configs:
please take this files as an examples, where you should replace the keywords ```YOUR.PUBLIC.IPv4```, ```YOUR.PUBLIC.IPv6``` and ```YOUR.DOMAIN.NAME```.

Enable support for standard HKP requests:
```
Listen YOUR.PUBLIC.IPv4:11371
NameVirtualHost YOUR.PUBLIC.IPv4:11371
Listen [YOUR.PUBLIC.IPv6]:11371
NameVirtualHost [YOUR.PUBLIC.IPv6]:11371
<VirtualHost YOUR.PUBLIC.IPv4:11371 [YOUR.PUBLIC.IPv6]:11371>
  ServerAdmin webmaster@localhost
  ServerName www.YOUR.DOMAIN.NAME
  ServerAlias YOUR.DOMAIN.NAME
  DocumentRoot /var/www/YOUR.DOMAIN.NAME/pub
  RewriteEngine on
  RewriteCond %{HTTP_HOST}  =www.YOUR.DOMAIN.NAME       [NC]
  RewriteRule ^(.*)         http://YOUR.DOMAIN.NAME$1  [R=301,NE]
	<Directory />
		Options FollowSymLinks
		AllowOverride None
	</Directory>
	<Directory /var/www/YOUR.DOMAIN.NAME/pub>
		Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Order allow,deny
		allow from all
	</Directory>

	ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/
	<Directory "/usr/lib/cgi-bin">
		AllowOverride None
		Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
		Order allow,deny
		Allow from all
	</Directory>

	ErrorLog ${APACHE_LOG_DIR}/error.log

	# Possible values include: debug, info, notice, warn, error, crit,
	# alert, emerg.
	LogLevel warn

	CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```
Enable support for HTTP requests:
```
Listen YOUR.PUBLIC.IPv4:80
NameVirtualHost YOUR.PUBLIC.IPv4:80
Listen [YOUR.PUBLIC.IPv6]:80
NameVirtualHost [YOUR.PUBLIC.IPv6]:80
<VirtualHost YOUR.PUBLIC.IPv4:80 [YOUR.PUBLIC.IPv6]:80>
  ServerAdmin webmaster@localhost
  ServerName www.YOUR.DOMAIN.NAME
  ServerAlias YOUR.DOMAIN.NAME pool.sks-keyservers.net *.pool.sks-keyservers.net
  DocumentRoot /var/www/YOUR.DOMAIN.NAME/pub
  RewriteEngine on
  RewriteCond %{HTTP_HOST}  =www.YOUR.DOMAIN.NAME       [NC]
  RewriteRule ^(.*)         http://YOUR.DOMAIN.NAME$1  [R=301,NE]
	<Directory />
		Options FollowSymLinks
		AllowOverride None
	</Directory>
	<Directory /var/www/YOUR.DOMAIN.NAME/pub>
		Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Order allow,deny
		allow from all
	</Directory>

	ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/
	<Directory "/usr/lib/cgi-bin">
		AllowOverride None
		Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
		Order allow,deny
		Allow from all
	</Directory>

	ErrorLog ${APACHE_LOG_DIR}/error.log

	# Possible values include: debug, info, notice, warn, error, crit,
	# alert, emerg.
	LogLevel warn

	CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```
Enable support for HTTPS requests:
```
<IfModule mod_ssl.c>
Listen YOUR.PUBLIC.IPv4:443
NameVirtualHost YOUR.PUBLIC.IPv4:443
Listen [YOUR.PUBLIC.IPv6]:443
NameVirtualHost [YOUR.PUBLIC.IPv6]:443
<VirtualHost YOUR.PUBLIC.IPv4:443 [YOUR.PUBLIC.IPv6]:443>
  ServerAdmin webmaster@localhost
  ServerName www.YOUR.DOMAIN.NAME
  ServerAlias YOUR.DOMAIN.NAME
  RewriteEngine on
  RewriteCond %{HTTP_HOST}  =www.YOUR.DOMAIN.NAME       [NC]
  RewriteRule ^(.*)         https://YOUR.DOMAIN.NAME$1  [R=301,NE]
	DocumentRoot /var/www/YOUR.DOMAIN.NAME/pub
	<Directory />
		Options FollowSymLinks
		AllowOverride None
	</Directory>
	<Directory /var/www/YOUR.DOMAIN.NAME/pub>
		Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Order allow,deny
		allow from all
	</Directory>

	ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/
	<Directory "/usr/lib/cgi-bin">
		AllowOverride None
		Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
		Order allow,deny
		Allow from all
	</Directory>

	ErrorLog ${APACHE_LOG_DIR}/error.log

	# Possible values include: debug, info, notice, warn, error, crit,
	# alert, emerg.
	LogLevel warn

	CustomLog ${APACHE_LOG_DIR}/ssl_access.log combined

	#   SSL Engine Switch:
	#   Enable/Disable SSL for this virtual host.
	SSLEngine on

	#   A self-signed (snakeoil) certificate can be created by installing
	#   the ssl-cert package. See
	#   /usr/share/doc/apache2.2-common/README.Debian.gz for more info.
	#   If both key and certificate are stored in the same file, only the
	#   SSLCertificateFile directive is needed.
	SSLCertificateFile  /etc/apache2/keys/YOUR.DOMAIN.NAME.crt
	SSLCertificateKeyFile  /etc/apache2/keys/YOUR.DOMAIN.NAME.key

	#   Server Certificate Chain:
	#   Point SSLCertificateChainFile at a file containing the
	#   concatenation of PEM encoded CA certificates which form the
	#   certificate chain for the server certificate. Alternatively
	#   the referenced file can be the same as SSLCertificateFile
	#   when the CA certificates are directly appended to the server
	#   certificate for convinience.
	#SSLCertificateChainFile /etc/apache2/ssl.crt/server-ca.crt
	SSLCertificateChainFile  /etc/apache2/keys/YOUR.DOMAIN.NAME.int

	#   Certificate Authority (CA):
	#   Set the CA certificate verification path where to find CA
	#   certificates for client authentication or alternatively one
	#   huge file containing all of them (file must be PEM encoded)
	#   Note: Inside SSLCACertificatePath you need hash symlinks
	#         to point to the certificate files. Use the provided
	#         Makefile to update the hash symlinks after changes.
	#SSLCACertificatePath /etc/ssl/certs/
	#SSLCACertificateFile /etc/apache2/ssl.crt/ca-bundle.crt

	#   Certificate Revocation Lists (CRL):
	#   Set the CA revocation path where to find CA CRLs for client
	#   authentication or alternatively one huge file containing all
	#   of them (file must be PEM encoded)
	#   Note: Inside SSLCARevocationPath you need hash symlinks
	#         to point to the certificate files. Use the provided
	#         Makefile to update the hash symlinks after changes.
	#SSLCARevocationPath /etc/apache2/ssl.crl/
	#SSLCARevocationFile /etc/apache2/ssl.crl/ca-bundle.crl

	#   Client Authentication (Type):
	#   Client certificate verification type and depth.  Types are
	#   none, optional, require and optional_no_ca.  Depth is a
	#   number which specifies how deeply to verify the certificate
	#   issuer chain before deciding the certificate is not valid.
	#SSLVerifyClient require
	#SSLVerifyDepth  10

	#   Access Control:
	#   With SSLRequire you can do per-directory access control based
	#   on arbitrary complex boolean expressions containing server
	#   variable checks and other lookup directives.  The syntax is a
	#   mixture between C and Perl.  See the mod_ssl documentation
	#   for more details.
	#<Location />
	#SSLRequire (    %{SSL_CIPHER} !~ m/^(EXP|NULL)/ \
	#            and %{SSL_CLIENT_S_DN_O} eq "Snake Oil, Ltd." \
	#            and %{SSL_CLIENT_S_DN_OU} in {"Staff", "CA", "Dev"} \
	#            and %{TIME_WDAY} >= 1 and %{TIME_WDAY} <= 5 \
	#            and %{TIME_HOUR} >= 8 and %{TIME_HOUR} <= 20       ) \
	#           or %{REMOTE_ADDR} =~ m/^192\.76\.162\.[0-9]+$/
	#</Location>

	#   SSL Engine Options:
	#   Set various options for the SSL engine.
	#   o FakeBasicAuth:
	#     Translate the client X.509 into a Basic Authorisation.  This means that
	#     the standard Auth/DBMAuth methods can be used for access control.  The
	#     user name is the `one line' version of the client's X.509 certificate.
	#     Note that no password is obtained from the user. Every entry in the user
	#     file needs this password: `xxj31ZMTZzkVA'.
	#   o ExportCertData:
	#     This exports two additional environment variables: SSL_CLIENT_CERT and
	#     SSL_SERVER_CERT. These contain the PEM-encoded certificates of the
	#     server (always existing) and the client (only existing when client
	#     authentication is used). This can be used to import the certificates
	#     into CGI scripts.
	#   o StdEnvVars:
	#     This exports the standard SSL/TLS related `SSL_*' environment variables.
	#     Per default this exportation is switched off for performance reasons,
	#     because the extraction step is an expensive operation and is usually
	#     useless for serving static content. So one usually enables the
	#     exportation for CGI and SSI requests only.
	#   o StrictRequire:
	#     This denies access when "SSLRequireSSL" or "SSLRequire" applied even
	#     under a "Satisfy any" situation, i.e. when it applies access is denied
	#     and no other module can change it.
	#   o OptRenegotiate:
	#     This enables optimized SSL connection renegotiation handling when SSL
	#     directives are used in per-directory context.
	#SSLOptions +FakeBasicAuth +ExportCertData +StrictRequire
	<FilesMatch "\.(cgi|shtml|phtml|php)$">
		SSLOptions +StdEnvVars
	</FilesMatch>
	<Directory /usr/lib/cgi-bin>
		SSLOptions +StdEnvVars
	</Directory>

	#   SSL Protocol Adjustments:
	#   The safe and default but still SSL/TLS standard compliant shutdown
	#   approach is that mod_ssl sends the close notify alert but doesn't wait for
	#   the close notify alert from client. When you need a different shutdown
	#   approach you can use one of the following variables:
	#   o ssl-unclean-shutdown:
	#     This forces an unclean shutdown when the connection is closed, i.e. no
	#     SSL close notify alert is send or allowed to received.  This violates
	#     the SSL/TLS standard but is needed for some brain-dead browsers. Use
	#     this when you receive I/O errors because of the standard approach where
	#     mod_ssl sends the close notify alert.
	#   o ssl-accurate-shutdown:
	#     This forces an accurate shutdown when the connection is closed, i.e. a
	#     SSL close notify alert is send and mod_ssl waits for the close notify
	#     alert of the client. This is 100% SSL/TLS standard compliant, but in
	#     practice often causes hanging connections with brain-dead browsers. Use
	#     this only for browsers where you know that their SSL implementation
	#     works correctly.
	#   Notice: Most problems of broken clients are also related to the HTTP
	#   keep-alive facility, so you usually additionally want to disable
	#   keep-alive for those clients, too. Use variable "nokeepalive" for this.
	#   Similarly, one has to force some clients to use HTTP/1.0 to workaround
	#   their broken HTTP/1.1 implementation. Use variables "downgrade-1.0" and
	#   "force-response-1.0" for this.
	BrowserMatch "MSIE [2-6]" \
		nokeepalive ssl-unclean-shutdown \
		downgrade-1.0 force-response-1.0
	# MSIE 7 and newer should be able to use keepalive
	BrowserMatch "MSIE [17-9]" ssl-unclean-shutdown

</VirtualHost>
</IfModule>
```

##### ..i really don't want a keyserver, but a webserver that uses ```gpg``` locally to answer the request?
hey, the other day i found https://github.com/remko/phkp, hope it helps!

### Very special thanks to:
- https://bitbucket.org/skskeyserver/sks-keyserver
- https://keyserver.mattrude.com
- https://pgp.mit.edu
- https://sks-keyservers.net
- https://github.com/jenssegers/php-proxy
- https://getcomposer.org/
- https://www.gnu.org/software/make/manual/make.html
- https://git-scm.com/book
