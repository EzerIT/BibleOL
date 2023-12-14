IMAGE_NAME = bibleol-amd
IMAGE_TAG = 2023_12_12
CONTAINER_NAME = bibleol-amd-container

build:
	docker image build -t tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG) .
	docker container run -dt -p 8000:80 --env-file environment.txt --name $(CONTAINER_NAME) tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	docker container exec $(CONTAINER_NAME) bash start.sh

clean:
	docker container rm -f $(CONTAINER_NAME) 
	docker image rm -f tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)

pull:
	docker pull tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	docker container run -dt -p 8000:80 --env-file environment.txt --name $(CONTAINER_NAME) tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	docker container exec $(CONTAINER_NAME) bash start.sh