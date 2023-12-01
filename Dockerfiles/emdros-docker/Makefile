build:
	docker image build -t tmccormack14/emdros-image:2023_11_20 .
	docker container run -d -t --name emdros tmccormack14/emdros-image:2023_11_20

clean:
	docker container rm -f emdros
	docker image rm -f tmccormack14/emdros-image:2023_11_20

push:
	docker buildx build --platform linux/arm64/v8,linux/amd64 --push --tag tmccormack14/emdros-image:2023_11_20 .