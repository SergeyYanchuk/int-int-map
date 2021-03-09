** Сборка и запуск тестов **
 * Собрать образ ```docker build -t custom-php8 ./docker```
 * Установить зависимости ```docker run -it -v $PWD:/app custom-php8 composer install```
 * Запустить тесты ```docker run -it -v $PWD:/app custom-php8 ./vendor/bin/phpunit ./tests```
 