sh get-docker.sh
#composer create-project symfony/skeleton app
docker-compose build --no-cache
cd frontend
npm update
npm upgrade
npm install