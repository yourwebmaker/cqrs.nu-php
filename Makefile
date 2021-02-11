SHELL=/bin/bash

ifndef PHP_DOCKER_COMMAND
PHP_DOCKER_COMMAND=docker-compose exec php-fpm
endif

# Mute all `make` specific output. Comment this out to get some debug information.
.SILENT:

.PHONY: help
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'



.PHONY: up
up: ## start docker containers
	- docker-compose up -d

.PHONY: down
down: ## stop docker containers
	- docker-compose down -v

.PHONY: status
status: ## List containers
	- docker-compose ps



.PHONY: install-dependencies
install-dependencies: ## Run composer install
	- ${PHP_DOCKER_COMMAND} composer install

.PHONY: migrations-run
migrations-run: ## Run database migrations
	- ${PHP_DOCKER_COMMAND} vendor/bin/doctrine-migrations migrations:migrate --configuration migrations/migrations-config.php --db-configuration migrations/db-config.php --no-interaction --ansi

.PHONY: prune
prune: ## Delete cache and log files
	- ${MAKE} prune-cache
	- ${MAKE} prune-logs

.PHONY: prune-all
prune-all: ## Delete vendor folder, cache and log files
	- sudo rm -fR vendor/*
	- ${MAKE} prune-cache
	- ${MAKE} prune-logs


.PHONY: prune-cache
prune-cache:
	- ${PHP_DOCKER_COMMAND} rm -fR var/cache/*

.PHONY: prune-logs
prune-logs:
	- ${PHP_DOCKER_COMMAND} rm -fR var/log/*



.PHONY: test
test: ## Run phpunit
	- ${PHP_DOCKER_COMMAND} vendor/bin/phpunit

# TODO STATIC ANALYSE
#.PHONY: analyse
#analyse: ## Run static analyse
#	- ${PHP_DOCKER_COMMAND} ...dev tool command

# TODO CODE STYLE CHECK
#.PHONY: cs
#cs: ## Check code style
#	- ${PHP_DOCKER_COMMAND} ...dev tool command --dry-run

# TODO CODE STYLE FIX
#.PHONY: cs-fix
#cs-fix: ## Fix code style
#	- ${PHP_DOCKER_COMMAND} ...dev tool command