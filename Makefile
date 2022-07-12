up: docker-up
init: docker-build docker-up

docker-up:
	docker-compose up -d

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-build:
	docker-compose build

schema-validate:
	docker-compose run --rm dev-symfony-php-cli bin/console doctrine:schema:validate

migrate-generate:
	docker-compose run --rm dev-symfony-php-cli bin/console doctrine:migrations:generate --no-interaction

migrate:
	docker-compose run --rm dev-symfony-php-cli bin/console doctrine:migrations:migrate --no-interaction

migrate-rollback:
	docker-compose run --rm dev-symfony-php-cli bin/console doctrine:migrations:migrate prev --no-interaction

test-unit:
	docker-compose run --rm dev-symfony-php-cli php vendor/bin/codecept run unit