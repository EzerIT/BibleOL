docker image build -t tmccormack14/bibleol-amd-img:2023_12_08 .
docker container run -d -t -p 8000:80 --env-file environment.txt --name bibleol-amd-container tmccormack14/bibleol-amd-img:2023_12_08
docker container exec bibleol-amd-container bash build_emdros.sh
docker container exec bibleol-amd-container bash config.sh
