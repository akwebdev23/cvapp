up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down docker-pull docker-build docker-up cvapp-init

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

cvapp-init: cvapp-composer-install cvapp-assets-install cvapp-wait-db cvapp-migrations


cvapp-composer-install:
	docker-compose run --rm cvapp-php-cli composer install

cvapp-assets-install:
	docker-compose run --rm cvapp-node yarn install
	docker-compose run --rm cvapp-node npm rebuild node-sass

cvapp-wait-db:
	until docker-compose exec -T cvapp-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

cvapp-migrations:
	docker-compose run --rm cvapp-php-cli php bin/console doctrine:migrations:migrate --no-interaction

cvapp-fixtures:
	docker-compose run --rm cvapp-php-cli php bin/console doctrine:fixtures:load --no-interaction

cvapp-assets-dev:
	docker-compose run --rm cvapp-node npm run dev

build-production:
	docker build --pull --file=docker/production/nginx.docker --tag ${REGISTRY_ADDRESS}/cvapp-nginx:${IMAGE_TAG} ./
	docker build --pull --file=docker/production/php-fpm.docker --tag ${REGISTRY_ADDRESS}/cvapp-php-fpm:${IMAGE_TAG} ./
	docker build --pull --file=docker/production/php-cli.docker --tag ${REGISTRY_ADDRESS}/cvapp-php-cli:${IMAGE_TAG} ./
	docker build --pull --file=docker/production/postgres.docker --tag ${REGISTRY_ADDRESS}/cvapp-postgres:${IMAGE_TAG} ./
	docker build --pull --file=docker/production/rabbitmq.docker --tag ${REGISTRY_ADDRESS}/cvapp-rabbitmq:${IMAGE_TAG} ./

push-production:
	docker push ${REGISTRY_ADDRESS}/cvapp-nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/cvapp-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/cvapp-php-cli:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/cvapp-postgres:${IMAGE_TAG}

deploy-production:
	docker-compose -f ./docker-compose-production.yml pull
	docker-compose -f ./docker-compose-production.yml up --build -d
	until docker-compose -f ./docker-compose-production.yml exec -T cvapp-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done
	docker-compose -f ./docker-compose-production.yml run --rm cvapp-php-cli php bin/console doctrine:migrations:migrate --no-interaction

deploy-test:
	docker-compose -f ./docker-compose-production.yml pull
	docker-compose -f ./docker-compose-production.yml -f ./docker-compose-test.yml up --build -d
	until docker-compose -f ./docker-compose-production.yml exec -T cvapp-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done
	docker-compose -f ./docker-compose-production.yml run --rm cvapp-php-cli php bin/console doctrine:migrations:migrate --no-interaction