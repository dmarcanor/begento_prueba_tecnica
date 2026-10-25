up:
	docker compose up -d

down:
	docker compose down -v

setup:
	docker volume create prueba_tecnica_postgres_data
	docker volume create prueba_tecnica_redis_data
	cp .env.example .env
	make setup-database
	make up
	make install
	make generate-laravel-key
	make migrate
	make seed

setup-database:
	@test -f .env || cp .env.example .env
	@echo "Configuring PostgreSQL connection in .env (press Enter to keep the default)"
	@printf "DB_CONNECTION [pgsql]: "; read -r conn; \
	conn=$${conn:-pgsql}; \
	printf "DB_HOST [postgres]: "; read -r host; \
	host=$${host:-postgres}; \
	printf "DB_PORT [5432]: "; read -r port; \
	port=$${port:-5432}; \
	printf "DB_DATABASE [prueba_tecnica]: "; read -r db; \
	db=$${db:-prueba_tecnica}; \
	printf "DB_USERNAME [pguser]: "; read -r user; \
	user=$${user:-pguser}; \
	printf "DB_PASSWORD [changeme]: "; \
	if [ -t 0 ]; then stty -echo; fi; \
	read -r pass; \
	if [ -t 0 ]; then stty echo; echo; fi; \
	pass=$${pass:-changeme}; \
	set_env() { \
		key=$$1; value=$$2; \
		if grep -q "^$$key=" .env; then \
			sed "s|^$$key=.*|$$key=$$value|" .env > .env.tmp && mv .env.tmp .env; \
		elif grep -q "^# $$key=" .env; then \
			sed "s|^# $$key=.*|$$key=$$value|" .env > .env.tmp && mv .env.tmp .env; \
		else \
			echo "$$key=$$value" >> .env; \
		fi; \
	}; \
	set_env DB_CONNECTION "$$conn"; \
	set_env DB_HOST "$$host"; \
	set_env DB_PORT "$$port"; \
	set_env DB_DATABASE "$$db"; \
	set_env DB_USERNAME "$$user"; \
	set_env DB_PASSWORD "$$pass"; \
	echo "Database configuration written to .env"

install:
	docker compose run --rm php composer install

generate-laravel-key:
	docker compose exec php php artisan key:generate

migrate:
	docker compose exec php php artisan migrate

rollback:
	docker compose exec php php artisan migrate:rollback

seed:
	docker compose exec php php artisan db:seed

test:
	docker compose exec php sh -c 'DB_DATABASE=prueba_tecnica_test php artisan migrate:fresh --force'
	docker compose exec php ./vendor/bin/pest --parallel