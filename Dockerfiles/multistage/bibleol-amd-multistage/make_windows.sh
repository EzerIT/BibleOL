docker_username='tmccormack14'
image_name='bibleol-amd-ms'
image_tag='2023_12_12'
container_name='bibleol-amd-container-ms'

docker image build -t $docker_username/$image_name:$image_tag .
docker container run -d -t -p 8000:80 --env-file environment.txt --name $container_name $docker_username/$image_name:$image_tag
docker container exec $container_name bash build_emdros.sh
docker container exec $container_name bash config.sh
