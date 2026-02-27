export USER_UID=$(shell id -u)
export USER_GID=$(shell id -g)

.PHONY: build php composer composer-install composer-dump-autoload
IMAGE_NAME = padi-php
IMAGE_FILE = .infra/docker/.image-built

$(IMAGE_FILE): .infra/docker/php.Dockerfile
	docker build -t ${IMAGE_NAME} -f .infra/docker/php.Dockerfile --build-arg UID=$(USER_UID) --build-arg GID=$(USER_GID) .
	touch $(IMAGE_FILE)

build: $(IMAGE_FILE)

# PHP
php: build
	docker run --rm -it -v $(PWD):/app $(IMAGE_NAME) php $(filter-out $@,$(MAKECMDGOALS))

# Composer
composer: build
	docker run --rm -it -v $(PWD):/app $(IMAGE_NAME) composer $(filter-out $@,$(MAKECMDGOALS))

composer-install:
	make composer install

composer-dump-autoload:
	make composer dump-autoload

# Avoid error when target not found, required to avoid error when use $(filter-out $@,$(MAKECMDGOALS)) to pass args
%:
	@:
