ERR=*** composer not found
HINT=Please, goto https://getcomposer.org and install it globally.

all: composer-install

quickstart:
	@echo
	@echo "If you want, run the following commands inside the main directory:"
	@echo "   make config     - if you need help to configure php-proxy-keyserver"
	@echo "   make skins      - if you wish to download extra skins"
	@echo "   make help       - if you wish to read extended help"
	
help:
	@echo
	@echo "Available commands inside the main directory:"
	@echo "   make            - install dependencies (if downloaded directly form github)"
	@echo "   make config     - create etc/php-proxy-keyserver.ini if not exists"
	@echo "   make skins      - download extra skins at skins/*"
	@echo "   make test       - run test suite"
	@echo "   make coverage   - send coverage report"
	@echo "   make debug      - follow output of logs"
	@echo "   make clean      - remove logs"
	@echo "   make quickstart - show minimal help"
	@echo "   make help       - show extended help"
	
config:
	@cd etc && test -e php-proxy-keyserver.ini || cp php-proxy-keyserver.ini.example php-proxy-keyserver.ini
	@echo
	@echo "----- PLEASE, EDIT YOUR CONFIG FILES -----"
	@echo
	@echo "1) Edit ${PWD}/etc/php-proxy-keyserver.ini"
	@echo "2) Set ${PWD}/pub as the DocumentRoot of your domain in your webserver configs."
	@echo
	@echo "When done, please visit your website and validate that you can search/retrieve/submit pgp public keys."

skins: .gitmodules
	@git submodule init
	@git submodule update

composer-install:
	$(if $(shell sh -c 'composer -v >/dev/null 2>&1 && echo 1'),,$(warning $(ERR));$(error $(HINT)))
	@composer self-update
	@composer install
	
test: test/phpunit.xml
	@vendor/bin/phpunit -c test

coverage: test/clover.xml
	@vendor/bin/coveralls -v -c test/.coveralls.yml

debug: log/php-proxy-keyserver.log
	@tail -f log/php-proxy-keyserver.log

clean: log
	@rm -rf log

.PHONY: test skins
