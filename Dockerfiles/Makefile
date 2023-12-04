run:
	docker container run -d -t -p 80:80  --name bibleol --env-file bibleol-docker/environment.txt tmccormack14/bibleol:2023_12_03
	docker container exec bibleol bash /config.sh

build:
	docker image build -t tmccormack14/emdros-image:2023_12_03 ./emdros-docker
	docker image build -t tmccormack14/bibleol:2023_12_03 ./bibleol-docker
	docker container run -d -t -p 80:80  --name bibleol --env-file bibleol-docker/environment.txt tmccormack14/bibleol:2023_12_03
	docker container exec bibleol bash /config.sh

pull:
	docker pull tmccormack14/emdros-image:2023_12_03
	docker pull tmccormack14/bibleol:2023_12_03

clean:
	docker container rm -f bibleol