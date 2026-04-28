IMAGE_NAME = bibleol-windows
IMAGE_TAG = 2026_04_27
CONTAINER_NAME = bibleol-windows-container

build:
	docker image build -t tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG) .
	docker container run -dt -p 8000:80 --env-file environment.txt --name $(CONTAINER_NAME) tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	docker container exec $(CONTAINER_NAME) bash start.sh

build2:
	docker image build -t tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG) .
	powershell -Command "$p = Join-Path (Get-Location) '../../' | Resolve-Path; docker container run -dt -p 8000:80 --env-file environment.txt --volume \"$($p.Path):/var/www/html/BibleOL\" --name $(CONTAINER_NAME) tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)"
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
