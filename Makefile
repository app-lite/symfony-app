up: docker-up
init: docker-down-clear docker-build docker-up docker-composer-install env-generate docker-migrate docker-fixtures docker-assets-install docker-assets-dev

docker-up:
	docker-compose up -d

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-build:
	docker-compose build

env-generate:
	cp .env .env.local

docker-composer-install:
	docker-compose run --rm dev-symfony-php-cli composer install

schema-validate:
	docker-compose run --rm dev-symfony-php-cli bin/console doctrine:schema:validate

migrate-generate:
	docker-compose run --rm dev-symfony-php-cli bin/console doctrine:migrations:generate --no-interaction

docker-migrate:
	docker-compose run --rm dev-symfony-php-cli bin/console doctrine:migrations:migrate --no-interaction

migrate-rollback:
	docker-compose run --rm dev-symfony-php-cli bin/console doctrine:migrations:migrate prev --no-interaction

docker-fixtures:
	docker-compose run --rm dev-symfony-php-cli php bin/console doctrine:fixtures:load --no-interaction

docker-assets-install:
	docker-compose run --rm dev-symfony-node yarn install

docker-assets-dev:
	docker-compose run --rm dev-symfony-node yarn run dev

test-unit:
	docker-compose run --rm dev-symfony-php-cli php vendor/bin/codecept run unitz

test-acceptance:
	docker-compose run --rm dev-symfony-php-cli php vendor/bin/codecept run acceptance

test:
	docker-compose run --rm dev-symfony-php-cli php vendor/bin/codecept run

php-cs-fixer-dry-run-with-diff:
	docker-compose run --rm dev-symfony-php-cli php vendor/bin/php-cs-fixer fix --dry-run --diff

php-cs-fixer-dry-run:
	docker-compose run --rm dev-symfony-php-cli php vendor/bin/php-cs-fixer fix --dry-run

php-cs-fixer-with-diff:
	docker-compose run --rm dev-symfony-php-cli php vendor/bin/php-cs-fixer fix --diff

php-cs-fixer:
	docker-compose run --rm dev-symfony-php-cli php vendor/bin/php-cs-fixer fix

deptrac-layers:
	docker-compose run --rm dev-symfony-php-cli php vendor/bin/deptrac analyse --config-file=deptrac-layers.yaml

deptrac-modules:
	docker-compose run --rm dev-symfony-php-cli php vendor/bin/deptrac analyse --config-file=deptrac-modules.yaml

phpstan:
	docker-compose run --rm dev-symfony-php-cli php -d memory_limit=-1 vendor/bin/phpstan analyse src tests