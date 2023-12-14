DOCKER_USER = tmccormack14
IMAGE_TAG = 2023_12_12
IMAGE_NAME = bibleol-arm-ms
CONTAINER_NAME = bibleol-arm-ms-container

build:
	time sudo docker image build -t $(DOCKER_USER)/$(IMAGE_NAME):$(IMAGE_TAG) .
	time sudo docker container run -d -t -p 8000:80 --env-file environment.txt --name $(CONTAINER_NAME) $(DOCKER_USER)/$(IMAGE_NAME):$(IMAGE_TAG)
	time sudo docker container exec $(CONTAINER_NAME) bash /build_emdros.sh
	time sudo docker container exec $(CONTAINER_NAME) bash /config.sh

clean:
	sudo docker container rm -f $(CONTAINER_NAME)
	sudo docker image rm -f $(DOCKER_USER)/$(IMAGE_NAME):$(IMAGE_TAG)
