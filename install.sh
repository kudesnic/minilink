sh get-docker.sh
#composer create-project symfony/skeleton app
docker-compose build --no-cache
docker-compose up
docker-compose exec php composer install
docker-compose exec php php bin/console doctrine:shema:create --force
docker-compose exec php php bin/console geoip2:update
