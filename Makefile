export USER_UID=$(shell id -u)
export USER_GID=$(shell id -g)

.PHONY: build run php composer composer-install composer-dump-autoload
IMAGE_NAME = padi-php
IMAGE_FILE = .infra/docker/.image-built

$(IMAGE_FILE): .infra/docker/php.Dockerfile
	docker build -t ${IMAGE_NAME} -f .infra/docker/php.Dockerfile --build-arg UID=$(USER_UID) --build-arg GID=$(USER_GID) .
	touch $(IMAGE_FILE)

build: $(IMAGE_FILE)

run: build
	docker run --rm -it -v $(PWD):/app $(IMAGE_NAME) $(CMD) $(ARGS)

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
