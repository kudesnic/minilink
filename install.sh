#sh get-docker.sh
#composer create-project symfony/skeleton app
#docker-compose build --no-cache
#docker-compose up -d
#docker-compose exec php php composer.phar install
docker-compose exec php php bin/console doctrine:schema:create
docker-compose exec php bin/console do:migrations:execute 20210404113226 --up
docker-compose exec php php bin/console doctrine:fixtures:load
docker-compose exec php php bin/console geoip2:update
#mkdir -p config/jwt
#openssl genpkey -out backend/config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
#openssl pkey -in backend/config/jwt/private.pem -out backend/config/jwt/public.pem -pubout
