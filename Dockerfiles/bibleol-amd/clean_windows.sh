image_name="tmccormack14/bibleol-amd:2023_12_12"
container_name="bibleol-amd-container"

docker container rm -f $container_name 
docker image rm -f $image_name