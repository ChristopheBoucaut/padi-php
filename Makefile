export USER_UID=$(shell id -u)
export USER_GID=$(shell id -g)

.PHONY: build run php composer composer-install composer-dump-autoload mago lint format format-check analyze fix phpunit test
.SILENT: fix
IMAGE_NAME = padi-php
IMAGE_FILE = .infra/docker/.image-built

$(IMAGE_FILE): .infra/docker/php.Dockerfile
	docker build -t ${IMAGE_NAME} -f .infra/docker/php.Dockerfile --build-arg UID=$(USER_UID) --build-arg GID=$(USER_GID) .
	touch $(IMAGE_FILE)

build: $(IMAGE_FILE)

run: build
	docker run --rm -it -v $(PWD):/app $(IMAGE_NAME) $(CMD) $(ARGS)

run-ci:
	docker run --rm -v $(PWD):/app $(IMAGE_NAME):latest $(CMD) $(ARGS)

# PHP
php: override CMD:=php
php: ARGS:=-a
php: run

# Composer
composer: override CMD:=composer
composer: run

composer-install: override ARGS:=install
composer-install: composer

composer-dump-autoload: override ARGS:=dump-autoload
composer-dump-autoload: composer

# Mago
mago: override CMD:=mago
mago: run

lint: override ARGS:=lint
lint: mago

format: override ARGS:=fmt
format: mago

format-check: override ARGS:=fmt --check
format-check: mago

analyze: override ARGS:=analyze
analyze: mago

# TODO: Improve logic to run all commands in one container to avoid start/stop container for each tasks
fix:
	$(MAKE) -s format
	$(MAKE) -s mago ARGS="lint --fix --format-after-fix"
	$(MAKE) -s mago ARGS="analyze --fix --format-after-fix"
	$(MAKE) -s lint
	$(MAKE) -s analyze

# PHPUnit
phpunit: override CMD:=./vendor/bin/phpunit
phpunit: run

test: ARGS:=tests
test: phpunit

# For CI
ci-install:
	$(MAKE) -s run-ci CMD=composer ARGS=install

ci-test:
	$(MAKE) -s run-ci CMD=./vendor/bin/phpunit ARGS=tests

ci-lint:
	$(MAKE) -s run-ci CMD=mago ARGS=lint

ci-format-check:
	$(MAKE) -s run-ci CMD=mago ARGS="fmt --check"

ci-analyze:
	$(MAKE) -s run-ci CMD=mago ARGS=analyze
