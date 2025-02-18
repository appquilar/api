PROJECT_NAME=symfony-docker

.PHONY: build start stop restart purge shell apache-shell db-shell integration-db-shell

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
