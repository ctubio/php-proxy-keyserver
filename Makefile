all: test

test: test/phpunit.xml
	@vendor/bin/phpunit -c test

clean:
	@rm -rf tmp

.PHONY: test
