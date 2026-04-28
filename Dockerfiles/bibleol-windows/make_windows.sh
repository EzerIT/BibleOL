image_name="tmccormack14/bibleol-windows:2026_04_27"
container_name="bibleol-windows-container"

docker image build -t $image_name .
docker container run -dt -p 8000:80 --env-file environment.txt --name $container_name $image_name
docker container exec $container_name bash start.sh

# If you want to run with volume mapping (equivalent to make build2):
# powershell -Command "$p = Join-Path (Get-Location) '../../' | Resolve-Path; docker container run -dt -p 8000:80 --env-file environment.txt --volume \"$($p.Path):/var/www/html/BibleOL\" --name $container_name $image_name"
