export USER_UID=$(shell id -u)
export USER_GID=$(shell id -g)

.PHONY: build php
IMAGE_NAME = padi-php

build:
	docker build -t ${IMAGE_NAME} -f .infra/docker/php.Dockerfile --build-arg UID=$(USER_UID) --build-arg GID=$(USER_GID) .

php: build
	docker run --rm -it -v $(PWD):/app $(IMAGE_NAME) php $(filter-out $@,$(MAKECMDGOALS))

# Composer
composer: build
	docker run --rm -it -v $(PWD):/app $(IMAGE_NAME) composer $(filter-out $@,$(MAKECMDGOALS))

composer-install:
	make composer install

%:
	@: