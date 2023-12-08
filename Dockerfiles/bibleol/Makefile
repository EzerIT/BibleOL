IMAGE_TAG = 2023_12_08
IMAGE_NAME = bibleol-img
CONTAINER_NAME = bibleol-container

build:
	time docker image build -t tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG) .
	time docker container run -d -t -p 80:80 --env-file environment.txt --name $(CONTAINER_NAME) tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	time docker container exec $(CONTAINER_NAME) bash /build_emdros.sh
	time docker container exec $(CONTAINER_NAME) bash /config.sh

clean:
	docker container rm -f $(CONTAINER_NAME)
	docker image rm -f tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
