# Определение переменных
DOCKER_COMPOSE = docker-compose
PHP_CONTAINER = webserver
CODE_PATH = /var/www/html/
CS_FIXER_PATH = vendor/friendsofphp/php-cs-fixer/php-cs-fixer

# Список правил

.PHONY: start
up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

fix:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php $(CODE_PATH)$(CS_FIXER_PATH) fix $(CODE_PATH)

shell:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh

e:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh -c '$(CMD)'

require:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh -c 'composer require $(CMD)'

operator__webhook_update:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh -c 'bin/console Telegram\\UpdateWebhook forRemittanceOperator'

logger__webhook_update:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh -c 'bin/console Telegram\\UpdateWebhook forLogger'

customer__webhook_update:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh -c 'bin/console Telegram\\UpdateWebhook forCustomer'

get_incomes:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh -c 'bin/console Cron\\Incomes proceed'

notify_operator:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh -c 'bin/console Cron\\Withdrawal createAndNotifyOperator'

approve_operator:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh -c 'bin/console RemittanceOperator approve $(ID)'

.PHONY: help start stop fix shell operator__webhook_update