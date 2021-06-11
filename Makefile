init: docker-down-clear docker-pull docker-build docker-up api-init
up: docker-up
down: docker-down
restart: down up
check: lint analyze test api-validate-schema
lint: api-lint
analyze: api-analyze
test: api-test
docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

api-clear:
	docker run --rm -v ${PWD}/api:/api -w /app alpine sh -c 'rm -rf var/*'

api-lint:
	docker-compose run --rm api-php-cli composer lint
	docker-compose run --rm api-php-cli composer cs-check

api-analyze:
	docker-compose run --rm api-php-cli composer psalm

api-test:
	docker-compose run --rm api-php-cli composer test

api-test-unit:
	docker-compose run --rm api-php-cli composer test -- --testsuite=unit

api-test-functional:
	docker-compose run --rm api-php-cli composer test -- --testsuite=functional

api-init: api-composer-install api-wait-db api-migrations api-fixtures

api-composer-install:
	docker-compose run --rm api-php-cli composer install

api-migrations:
	docker-compose run --rm api-php-cli composer app migrations:migrate --no-interaction

api-fixtures:
	docker-compose run --rm api-php-cli composer app fixtures:load

api-validate-schema:
	docker-compose run --rm api-php-cli composer app orm:validate-schema

api-wait-db:
	docker-compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 30

build: build-gateway build-frontend build-api

build-gateway:
	docker --log-level=debug build --pull --file=gateway/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-gateway:${IMAGE_TAG} gateway/docker

build-frontend:
	docker --log-level=debug build --pull --file=frontend/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-frontend:${IMAGE_TAG} frontend

build-api:
	docker --log-level=debug build --pull --file=api/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-api:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/auction-api-php-fpm:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-cli/Dockerfile --tag=${REGISTRY}/auction-api-php-cli:${IMAGE_TAG} api
try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

push: push-gateway push-frontend push-api

push-gateway:
	docker push ${REGISTRY}/auction-gateway:${IMAGE_TAG}

push-frontend:
	docker push ${REGISTRY}/auction-frontend:${IMAGE_TAG}

push-api:
	docker push ${REGISTRY}/auction-api:${IMAGE_TAG}
	docker push ${REGISTRY}/auction-api-php-fpm:${IMAGE_TAG}