ERR=*** composer not found
HINT=Please, goto https://getcomposer.org and install it globally.

all: install-composer

test: test/phpunit.xml
	@vendor/bin/phpunit -c test

coverage: test/clover.xml
	@vendor/bin/coveralls -v -c test/.coveralls.yml

skins: .gitmodules
	@git submodule init
	@git submodule update

config:
	@cd etc && test -e php-proxy-keyserver.ini || cp php-proxy-keyserver.ini.example php-proxy-keyserver.ini
	@echo
	@echo "----- PLEASE, EDIT YOUR CONFIGS, NOW -----"
	@echo
	@echo "1) Edit ${PWD}/etc/php-proxy-keyserver.ini"
	@echo "2) Set ${PWD}/pub as the DocumentRoot of your domain in your webserver configs."
	@echo
	@echo "When done, please visit your website and validate that you can search/retrieve/submit pgp public keys."

install-composer:
	$(if $(shell sh -c 'composer -v >/dev/null 2>&1 && echo 1'),,$(warning $(ERR));$(error $(HINT)))
	@composer self-update
	@composer install

debug: log/php-proxy-keyserver.log
	@tail -f log/php-proxy-keyserver.log

clean: log
	@rm -rf log

.PHONY: test skins
