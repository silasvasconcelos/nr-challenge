### Web Crawler 
Project to test made in laravel 5.3

**Server dependencies**:
 - Redis
 - MariaDB
 - PHP >= 5.6.4 (Recommended 7.1)
 - Nginx or Apache how reverse proxy (it's up to you)

**Packages dependencies**:
[Symfony DomCrawler Component](https://symfony.com/doc/current/components/dom_crawler.html)
*The DomCrawler component eases DOM navigation for HTML and XML documents.*

[Guzzle, PHP HTTP client](http://docs.guzzlephp.org/en/stable/)
*Guzzle is a PHP HTTP client that makes it easy to send HTTP requests and trivial to integrate with web services.*

**Deploy:**
clone this repository: `git clone https://github.com/silasvasconcelos/nr-challenge` 
enter into the folder: `cd nr-challenge`
generate APP_KEY: `php artisan key:generate`
install packages with composer: `composer install`
configurer your Redis and MariaDB connection on .env
*MariaDB*

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=homestead
    DB_USERNAME=homestead
    DB_PASSWORD=secret
   
   *Redis*

    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379

Run lavel queue's: `php artisan queue:work --timeout=1800 --timeout=1800 --sleep=3 --tries=3`

> 1800 seconds it's equal 30 minutes, recommended to job's

it's recommended use the Supervisor in production, more [here](https://laravel.com/docs/5.3/queues#supervisor-configuration)
```php
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /local/to/your/project/artisan queue:work --timeout=1800 --sleep=3 --tries=3
autostart=true
autorestart=true
user=forge
numprocs=8
redirect_stderr=true
stdout_logfile=/home/forge/app.com/worker.log
```
if you want run the application local, run `php artisan serve` after access `http://localhost:8000`