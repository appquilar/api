PROJECT_NAME=symfony-docker

DC=docker-compose
DC_STAGING=$(DC) -f docker-compose.yml -f docker-compose.staging.yml
DC_PROD=$(DC) -f docker-compose.yml -f docker-compose.prod.yml

.PHONY: \
	build start stop restart purge \
	shell apache-shell db-shell integration-db-shell \
	unit-tests unit-tests-coverage integration-tests all-tests \
	clean-integration \
	ci-copy-env ci-migrations run-ci-unit-tests run-ci-integration-tests \
	setup \
	start-dev \
	build-staging start-staging stop-staging \
	build-prod start-prod stop-prod

## Build the Docker images (dev)
build:
	@echo "Building Docker images for DEV..."
	$(DC) build

## Start the Docker containers (dev)
start: build
	@echo "Starting Docker containers for DEV..."
	$(DC) up -d

## Alias: start-dev -> start
start-dev: start

## Stop the Docker containers (dev)
stop:
	@echo "Stopping Docker containers for DEV..."
	$(DC) down

## Restart the Docker containers (dev)
restart: stop start

## Purge all Docker data (volumes and containers) in dev
purge:
	@echo "Removing all Docker containers and volumes for DEV..."
	$(DC) down -v
	docker system prune -a --volumes -f

## Enter the Symfony PHP container shell (dev)
shell:
	@echo "Entering PHP container shell (DEV)..."
	docker exec -it $$( $(DC) ps -q php ) bash

## Enter the MySQL Database shell (dev)
db-shell:
	@echo "Entering MySQL database shell (DEV)..."
	docker exec -it $$( $(DC) ps -q mysql ) mysql -u symfony_user -p

## Enter the MySQL Integration Database shell (dev)
integration-db-shell:
	@echo "Entering MySQL integration database shell (DEV)..."
	docker exec -it $$( $(DC) ps -q mysql_integration ) mysql -u symfony_test_user -p

## -----------------------------
## CI / Tests (se ejecutan ya dentro de un entorno preparado)
## -----------------------------

ci-migrations:
	@echo "Generating integration environment (CI)..."
	bin/console doctrine:database:create --if-not-exists --no-interaction
	bin/console doctrine:migrations:migrate --no-interaction

run-ci-unit-tests:
	@echo "Running Unit Tests (CI)..."
	bin/phpunit --testsuite=Unit --testdox

run-ci-integration-tests:
	@echo "Running Integration Tests (CI)..."
	bin/phpunit --testsuite=Unit --testdox

unit-tests:
	@echo "Running Unit Tests (DEV)..."
	docker exec -it $$( $(DC) ps -q php ) bin/phpunit --testsuite=Unit --testdox

unit-tests-coverage:
	@echo "Running Unit Tests with coverage (DEV)..."
	XDEBUG_MODE=coverage docker exec -it $$( $(DC) ps -q php ) bin/phpunit --coverage-html build/coverage-html --coverage-clover build/logs/clover.xml

integration-tests:
	@echo "Running Integration Tests (DEV)..."
	docker exec -it $$( $(DC) ps -q php ) bin/phpunit --testsuite=Integration --testdox

all-tests:
	@echo "Running All Tests (DEV)..."
	docker exec -it $$( $(DC) ps -q php ) bin/phpunit --testdox

clean-integration:
	@echo "Resetting integration database (DEV/test)..."
	docker exec -it $$( $(DC) ps -q php ) bin/console doctrine:database:drop --force --if-exists --env=test
	docker exec -it $$( $(DC) ps -q php ) bin/console doctrine:database:create --if-not-exists --env=test
	docker exec -it $$( $(DC) ps -q php ) bin/console doctrine:migrations:migrate --no-interaction --env=test

ci-copy-env:
	cp .env.integration .env

setup:
	cp .env.dev .env

## -----------------------------
## STAGING
## -----------------------------

## Build images for staging (using docker-compose.staging.yml)
build-staging:
	@echo "Building Docker images for STAGING..."
	$(DC_STAGING) build

## Start staging stack
start-staging: build-staging
	@echo "Starting Docker containers for STAGING..."
	$(DC_STAGING) up -d

## Stop staging stack
stop-staging:
	@echo "Stopping Docker containers for STAGING..."
	$(DC_STAGING) down

## -----------------------------
## PRODUCTION
## -----------------------------

## Build images for production (using docker-compose.prod.yml)
build-prod:
	@echo "Building Docker images for PRODUCTION..."
	$(DC_PROD) build

## Start production stack
start-prod: build-prod
	@echo "Starting Docker containers for PRODUCTION..."
	$(DC_PROD) up -d

## Stop production stack
stop-prod:
	@echo "Stopping Docker containers for PRODUCTION..."
	$(DC_PROD) down
