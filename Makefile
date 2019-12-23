COMPOSER_CMD=composer
PHIVE_CMD=phive

BOX_CMD=tools/box
PHPUNIT_CMD=tools/phpunit
PHPCS_CMD=tools/phpcs

.DEFAULT_GOAL=all

TARGET=inroute.phar

SRC_FILES:=$(shell find src/ -type f -name '*.php')

$(TARGET): vendor/installed $(SRC_FILES) box.json $(BOX_CMD)
	$(BOX_CMD) compile

.PHONY: all
all: test build

.PHONY: build
build: $(TARGET)

.PHONY: clean
clean:
	rm -f $(TARGET)
	rm -rf vendor
	rm -rf tools

.PHONY: test
test: phpunit phpcs

.PHONY: phpunit
phpunit: vendor/installed $(PHPUNIT_CMD)
	$(PHPUNIT_CMD)

.PHONY: phpcs
phpcs: $(PHPCS_CMD)
	$(PHPCS_CMD)

composer.lock: composer.json
	@echo composer.lock is not up to date

vendor/installed: composer.lock
	$(COMPOSER_CMD) install --prefer-dist
	touch $@

.PHONY: tools
tools: $(BOX_CMD) $(PHPUNIT_CMD) $(PHPCS_CMD)

$(BOX_CMD):
	$(PHIVE_CMD) install humbug/box:3 --force-accept-unsigned

$(PHPUNIT_CMD):
	$(PHIVE_CMD) install phpunit:8

$(PHPCS_CMD):
	$(PHIVE_CMD) install phpcs
