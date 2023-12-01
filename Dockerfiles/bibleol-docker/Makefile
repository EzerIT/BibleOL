build:
	docker image build -t tmccormack14/bibleol:2023_11_30 .
	docker container run -d -t -p 80:80  --name bibleol --env-file environment.txt tmccormack14/bibleol:2023_11_30
	docker container exec bibleol bash /config.sh
clean:
	docker container rm -f bibleol
	docker image rm -f tmccormack14/bibleol:2023_11_30
