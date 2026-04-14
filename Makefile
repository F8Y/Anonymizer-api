COMPOSE=docker compose
APP=app
COMPOSER=$(COMPOSE) run --rm composer

.PHONY: help init builder up down restart logs ps sh deps update autoload test clean

help:
	@echo "Доступные команды:"
	@echo " make init - установить зависимости, собрать и поднять проект"
	@echo " make build - собрать контейнеры"
	@echo " make up - поднять контейнеры"
	@echo " make down - остановить контейнеры"
	@echo " make restart - перезапустить контейнеры"
	@echo " make logs - смотреть логи app"
	@echo " make ps - список контейнеров"
	@echo " make sh - зайти в shell контейнера и app"
	@echo "make deps - установить composer-зависимости"
	@echo "make update - обновить composer-зависимости"
	@echo "make autoload - пересобрать autoload"
	@echo "make test - запустить тесты"
	@echo "make clean - остановить и удалить контейнеры"

init: deps build up

build:
	$(COMPOSE) build

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

restart:
	$(COMPOSE) down
	$(COMPOSE) up -d

logs:
	$(COMPOSE) logs -f $(APP)

ps:
	$(COMPOSE) ps

sh:
	$(COMPOSE) exec $(APP) sh

deps:
	$(COMPOSER) install

update:
	$(COMPOSER) update

autoload:
	$(COMPOSER) dump-autoload

test:
	$(COMPOSE) exec $(APP) ./vendor/bin/phpunit

clean:
	$(COMPOSE) down -v --remote-orphans