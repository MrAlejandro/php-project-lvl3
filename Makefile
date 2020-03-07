start:
	docker-compose up

bash:
	docker-compose run app bash

test:
	composer phpunit

deploy:
	git push heroku

lint:
	composer phpcs

lint-fix:
	composer phpcbf
