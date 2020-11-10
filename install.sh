sh get-docker.sh
#composer create-project symfony/skeleton app
docker-compose build --no-cache
docker-compose up -d
docker-compose exec php composer install
docker-compose exec php php bin/console doctrine:shema:create --force
docker-compose exec php php bin/console geoip2:update
mkdir -p config/jwt
openssl genpkey -out backend/config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
openssl pkey -in backend/config/jwt/private.pem -out backend/config/jwt/public.pem -pubout
