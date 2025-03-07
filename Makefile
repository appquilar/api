PROJECT_NAME=symfony-docker

.PHONY: build start stop restart purge shell apache-shell db-shell integration-db-shell unit-tests integration-tests all-tests clean-integration ci-copy-env ci-migrations run-ci-unit-tests run-ci-integration-tests setup

## Build the Docker images
build:
	@echo "Building Docker images..."
	docker-compose build

## Start the Docker containers
start: build
	@echo "Starting Docker containers..."
	docker-compose up -d

## Stop the Docker containers
stop:
	@echo "Stopping Docker containers..."
	docker-compose down

## Restart the Docker containers
restart: stop start

## Purge all Docker data (volumes and containers)
purge:
	@echo "Removing all Docker containers and volumes..."
	docker-compose down -v
	docker system prune -a --volumes -f

## Enter the Symfony PHP container shell
shell:
	@echo "Entering PHP container shell..."
	docker exec -it $$(docker-compose ps -q php) bash

## Enter the MySQL Database shell
db-shell:
	@echo "Entering MySQL database shell..."
	docker exec -it $$(docker-compose ps -q mysql) mysql -u symfony_user -p

## Enter the MySQL Integration Database shell
integration-db-shell:
	@echo "Entering MySQL integration database shell..."
	docker exec -it $$(docker-compose ps -q mysql_integration) mysql -u symfony_test_user -p

ci-migrations:
	@echo "Generating integration environment..."
	bin/console doctrine:database:create --if-not-exists --no-interaction
	bin/console doctrine:migrations:migrate --no-interaction

run-ci-unit-tests:
	@echo "Running Unit Tests..."
	bin/phpunit --testsuite=Unit --testdox

run-ci-integration-tests:
	@echo "Running Unit Tests..."
	bin/phpunit --testsuite=Unit --testdox

unit-tests:
	@echo "Running Unit Tests..."
	docker exec -it $$(docker-compose ps -q php) bin/phpunit --testsuite=Unit --testdox

integration-tests:
	@echo "Running Integration Tests..."
	docker exec -it $$(docker-compose ps -q php) bin/phpunit --testsuite=Integration --testdox

all-tests:
	@echo "Running All Tests..."
	docker exec -it $$(docker-compose ps -q php) bin/phpunit --testdox

clean-integration:
	@echo "Resetting database..."
	docker exec -it $$(docker-compose ps -q php) bin/console doctrine:database:drop --force --if-exists --env=test
	docker exec -it $$(docker-compose ps -q php) bin/console doctrine:database:create --env=test
	docker exec -it $$(docker-compose ps -q php) bin/console doctrine:migrations:migrate --no-interaction --env=test

ci-copy-env:
	cp .env.integration .env

setup:
	cp .env.dev .env