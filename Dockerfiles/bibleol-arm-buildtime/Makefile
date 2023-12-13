build:
	docker image build -t tmccormack14/bibleol-arm-bt:2023_12_13 .
	docker container run -dt -p 8000:80 --env-file environment.txt --name bibleol-arm-bt-container tmccormack14/bibleol-arm-bt:2023_12_13
	docker container exec bibleol-arm-bt-container bash start.sh
	
clean:
	docker container rm -f bibleol-arm-bt-container 
	docker image rm -f tmccormack14/bibleol-arm-bt:2023_12_13