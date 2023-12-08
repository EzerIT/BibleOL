IMAGE_TAG = 2023_12_08
IMAGE_NAME = bibleol-img
CONTAINER_NAME = bibleol-container

build:
	time sudo docker image build -t tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG) .
	time sudo docker container run -d -t -p 80:80 --env-file environment.txt --name $(CONTAINER_NAME) tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	time sudo docker container exec $(CONTAINER_NAME) bash /build_emdros.sh
	time sudo docker container exec $(CONTAINER_NAME) bash /config.sh

clean:
	sudo docker container rm -f $(CONTAINER_NAME)
	sudo docker image rm -f tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
