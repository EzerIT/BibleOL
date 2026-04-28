# Detect OS
ifeq ($(OS),Windows_NT)
    PLATFORM = windows
    PROJECT_ROOT = $(CURDIR)/../..
    # Normalize path for Docker volume mapping on Windows
    PROJECT_ROOT_DOCKER = $(subst \,/,$(shell cygpath -w $(PROJECT_ROOT) 2>/dev/null || echo $(PROJECT_ROOT)))
else
    UNAME_S := $(shell uname -s)
    ifeq ($(UNAME_S),Linux)
        PLATFORM = linux
    endif
    ifeq ($(UNAME_S),Darwin)
        PLATFORM = macosx
    endif
    PROJECT_ROOT = $(shell realpath ../..)
    PROJECT_ROOT_DOCKER = $(PROJECT_ROOT)
endif

# Detect Architecture
ifeq ($(OS),Windows_NT)
    # On Windows, we can check PROCESSOR_ARCHITECTURE
    # Usually AMD64 or ARM64
    ifeq ($(PROCESSOR_ARCHITECTURE),ARM64)
        DETECTED_ARCH = arm
        DETECTED_EMDROS_DIR = emdros_arm
    else ifeq ($(PROCESSOR_ARCHITEW6432),ARM64)
        DETECTED_ARCH = arm
        DETECTED_EMDROS_DIR = emdros_arm
    else
        DETECTED_ARCH = amd
        DETECTED_EMDROS_DIR = emdros_amd
    endif
else
    UNAME_M := $(shell uname -m)
    ifneq (,$(filter $(UNAME_M),x86_64 amd64))
        DETECTED_ARCH = amd
        DETECTED_EMDROS_DIR = emdros_amd
    else ifneq (,$(filter $(UNAME_M),arm64 aarch64))
        DETECTED_ARCH = arm
        DETECTED_EMDROS_DIR = emdros_arm
    else
        # Default to amd if unsure
        DETECTED_ARCH = amd
        DETECTED_EMDROS_DIR = emdros_amd
    endif
endif

ARCH ?= $(DETECTED_ARCH)
EMDROS_DIR ?= $(if $(filter $(ARCH),arm),emdros_arm,emdros_amd)

IMAGE_NAME = bibleol-$(ARCH)
ifeq ($(PLATFORM),windows)
    IMAGE_NAME = bibleol-windows
    IMAGE_TAG = 2026_04_27
else
    IMAGE_TAG = 2023_12_12
endif

CONTAINER_NAME = $(IMAGE_NAME)-container

# Build Arguments for Docker
BUILD_ARGS = --build-arg EMDROS_DIR=$(EMDROS_DIR) --build-arg PLATFORM=$(PLATFORM)

.PHONY: build build2 stop start clean pull

build:
	docker image build $(BUILD_ARGS) -t tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG) .
	docker container run -dt -p 8000:80 --env-file environment.txt --name $(CONTAINER_NAME) tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	docker container exec $(CONTAINER_NAME) bash start.sh

build2:
ifeq ($(PLATFORM),windows)
	@echo "Running build2 for Windows ($(ARCH))..."
	build2_windows.bat $(ARCH)
else
	docker image build $(BUILD_ARGS) -t tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG) .
	docker container run -dt -p 8000:80 --env-file environment.txt --volume $(PROJECT_ROOT_DOCKER):/var/www/html/BibleOL --name $(CONTAINER_NAME) tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	docker container exec $(CONTAINER_NAME) bash start.sh
endif

stop:
	docker container stop $(CONTAINER_NAME)

start:
	docker container start $(CONTAINER_NAME)
	docker container exec $(CONTAINER_NAME) bash start.sh

clean:
	docker container rm -f $(CONTAINER_NAME) 
	docker image rm -f tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	rm -rf $(PROJECT_ROOT)/.htaccess

pull:
	docker pull tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	docker container run -dt -p 8000:80 --env-file environment.txt --name $(CONTAINER_NAME) tmccormack14/$(IMAGE_NAME):$(IMAGE_TAG)
	docker container exec $(CONTAINER_NAME) bash start.sh
