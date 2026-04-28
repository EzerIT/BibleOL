@echo off
setlocal

:: Get the absolute path of the grandparent directory
:: %~dp0 is the directory of the script (Dockerfiles/bibleol-windows/)
pushd "%~dp0..\.."
set "PROJECT_ROOT=%CD%"
popd

echo Building image...
docker image build -t tmccormack14/bibleol-windows:2026_04_27 .

echo Starting container with volume mapping...
echo Project root: %PROJECT_ROOT%

docker container run -dt -p 8000:80 --env-file environment.txt --volume "%PROJECT_ROOT%:/var/www/html/BibleOL" --name bibleol-windows-container tmccormack14/bibleol-windows:2026_04_27

docker container exec bibleol-windows-container bash start.sh
