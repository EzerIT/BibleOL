image_name="tmccormack14/bibleol-amd:2023_12_12"
container_name="bibleol-amd-container"

docker image build -t $image_name .
docker container run -dt -p 8000:80 --env-file environment.txt --name $container_name $image_name
docker container exec $container_name bash start.sh
