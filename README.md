<h1>Minilnk API</h1>
<p>
    It is an application providing a simple API for link minification and visits tracking/analysis.
    The app uses Max Mind geo ip database for country detecting and as minified link value app uses guids generated by trigger and special procedure on the database side. Such an approach makes app faster and more reliable
   In the frontend directory, you can find a front-end boilerplate based on which you can build a client implementation. Unfortunately, I don't have enough time for that...<br>
To install it use install.sh script. This script will try to install docker on your pc and set up a virtual environment for the application. Then rename .env.example to .env and set JWT_PASSPHRASE to the value u set while installing <br>
  If it got failed, then you may try to install it manually using the below commands: 
  </p>
  </p>
  docker-compose build --no-cache</br>
  docker-compose up</br>
  docker-compose exec php composer install</br>
  docker-compose exec php php bin/console doctrine:shema:create --force</br>
  docker-compose exec php php bin/console geoip2:update</br>
  mkdir -p config/jwt</br>
  openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096</br>
  openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout</br>
</p>

<h2>For testing purposes you can use postman by importing environment and collection.</h2>
<p>Postman Env file - minilink.postman_environment</p>
<p>Collection file minilink.postman_collection</p>
