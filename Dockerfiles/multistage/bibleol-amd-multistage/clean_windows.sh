docker_username='tmccormack14'
image_name='bibleol-amd-ms'
image_tag='2023_12_12'
container_name='bibleol-amd-container-ms'

docker container rm -f $container_name
docker image rm -f $docker_username/$image_name:$image_tag
