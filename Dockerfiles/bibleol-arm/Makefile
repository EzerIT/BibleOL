IMAGE_NAME = bibleol-arm
IMAGE_TAG = 2023_12_12
CONTAINER_NAME = bibleol-arm-container

build:
	docker image build -t tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG) .
	docker container run -dt -p 8000:80 --env-file environment.txt --name $(CONTAINER_NAME) tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	docker container exec $(CONTAINER_NAME) bash start.sh

build:
	docker image build -t tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG) .
	docker container run -dt -p 8000:80 --env-file environment.txt --volume ~/BibleOL/myapp:/var/www/html/BibleOL/myapp --name $(CONTAINER_NAME) tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	docker container exec $(CONTAINER_NAME) bash start.sh

stop:
	docker container stop $(CONTAINER_NAME)

start:
	docker container start $(CONTAINER_NAME)
	docker container exec $(CONTAINER_NAME) bash start.sh

clean:
	docker container rm -f $(CONTAINER_NAME) 
	docker image rm -f tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)

pull:
	docker pull tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	docker container run -dt -p 8000:80 --env-file environment.txt --name $(CONTAINER_NAME) tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	docker container exec $(CONTAINER_NAME) bash start.sh
