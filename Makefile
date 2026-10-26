.PHONY: up down setup setup-database install generate-laravel-key migrate rollback seed test

up:
	docker compose up -d

down:
	docker compose down

setup:
	@test -f .env || cp .env.example .env
	docker volume create prueba_tecnica_postgres_data 2>/dev/null || true
	@echo "Generando HOST_UID y HOST_GID automáticamente..."
	@CURRENT_UID=$$(id -u); \
	CURRENT_GID=$$(id -g); \
	sed -i.bak "s/^HOST_UID=.*/HOST_UID=$$CURRENT_UID/" .env; \
	sed -i.bak "s/^HOST_GID=.*/HOST_GID=$$CURRENT_GID/" .env; \
	rm -f .env.bak
	@echo "Configurando variables de entorno de la base de datos..."
	@make setup-database
	docker compose build --no-cache
	make up
	make install
	make generate-laravel-key
	make migrate
	make seed

setup-database:
	@test -f .env || cp .env.example .env
	@echo "Configurando PostgreSQL en .env (presiona Enter para mantener el valor por defecto)"
	@printf "DB_CONNECTION [pgsql]: "; read -r conn; \
	conn=$${conn:-pgsql}; \
	printf "DB_HOST [postgres]: "; read -r host; \
	host=$${host:-postgres}; \
	printf "DB_PORT [5432]: "; read -r port; \
	port=$${port:-5432}; \
	printf "DB_DATABASE [prueba_tecnica]: "; read -r db; \
	db=$${db:-prueba_tecnica}; \
	printf "DB_USERNAME [prueba_tecnica_user]: "; read -r user; \
	user=$${user:-prueba_tecnica_user}; \
	printf "DB_PASSWORD: "; \
	if [ -t 0 ]; then stty -echo; fi; \
	read -r pass; \
	if [ -t 0 ]; then stty echo; echo; fi; \
	if [ -z "$$pass" ]; then echo "Error: DB_PASSWORD no puede estar vacío"; exit 1; fi; \
	awk -v key="DB_CONNECTION" -v val="$$conn" 'BEGIN{FS=OFS="="} $$1==key{$$0=key"="val} {print}' .env > .env.tmp && mv .env.tmp .env; \
	awk -v key="DB_HOST" -v val="$$host" 'BEGIN{FS=OFS="="} $$1==key{$$0=key"="val} {print}' .env > .env.tmp && mv .env.tmp .env; \
	awk -v key="DB_PORT" -v val="$$port" 'BEGIN{FS=OFS="="} $$1==key{$$0=key"="val} {print}' .env > .env.tmp && mv .env.tmp .env; \
	awk -v key="DB_DATABASE" -v val="$$db" 'BEGIN{FS=OFS="="} $$1==key{$$0=key"="val} {print}' .env > .env.tmp && mv .env.tmp .env; \
	awk -v key="DB_USERNAME" -v val="$$user" 'BEGIN{FS=OFS="="} $$1==key{$$0=key"="val} {print}' .env > .env.tmp && mv .env.tmp .env; \
	awk -v key="DB_PASSWORD" -v val="$$pass" 'BEGIN{FS=OFS="="} $$1==key{$$0=key"="val} {print}' .env > .env.tmp && mv .env.tmp .env; \
	echo "Configuración de base de datos escrita en .env"

install:
	docker compose run --rm php composer install

generate-laravel-key:
	docker compose exec php php artisan key:generate --force

migrate:
	docker compose exec php php artisan migrate

rollback:
	docker compose exec php php artisan migrate:rollback

seed:
	docker compose exec php php artisan db:seed

test:
	docker compose exec php ./vendor/bin/phpunit
