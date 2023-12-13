build:
	docker image build -t tmccormack14/bibleol-amd-bt:2023_12_12 .
	docker container run -dt -p 8000:80 --env-file environment.txt --name bibleol-amd-bt-container tmccormack14/bibleol-amd-bt:2023_12_12
	docker container exec bibleol-amd-bt-container bash start.sh
clean:
	docker container rm -f bibleol-amd-bt-container 
	docker image rm -f tmccormack14/bibleol-amd-bt:2023_12_12