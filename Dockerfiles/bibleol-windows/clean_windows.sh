container_name="bibleol-windows-container"
image_name="tmccormack14/bibleol-windows:2026_04_27"

docker container rm -f $container_name 
docker image rm -f $image_name
