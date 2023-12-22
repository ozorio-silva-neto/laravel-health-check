## Health Check Command Api

With this package you can check the health of your API, add this command
to your cronjob and be notified by email and log. You are free to create your own
verifications.

#### Services verified:
- Server
- Database
- SMTP
- Redis
- AWS
- Payment Gateway


### For package development
```bash
"autoload": {
  "psr-4": {
    "App\\": "app/",
    "App\\Modules\\": "app/Modules",
    "Database\\Factories\\": "database/factories/",
    "Database\\Seeders\\": "database/seeders/",
    "Ozoriotsn\\HealthCheckCmd\\": "packages/ozoriotsn/health-check-cmd/src/"
  }
}
composer update for update
```

https://opensource.com/article/22/5/composer-git-repositories
### To use package in another project
```bash
"repositories": [
{
   "type":"package",
   "package":{
      "name":"mynamespace/my-custom-theme",
      "version":"1.2.3",
      "type":"drupal-theme",
      "source":{
         "url":"https://github.com/mynamespace/my-custom-theme.git",
         "type":"git",
         "reference":"master"
      }
   }
}
]
```

### Para funcionar tem que adicionar o service provider do pacote:

Add the service provider in laravel at
config/app.php
```
providers [
  Ozoriotsn\HealthCheckCmd\Providers\HealthCheckProvider::class
]
```

To test, run the command:

```
php artisan health-check-cmd:api
```

### Add cron to check api health
em app/Console/Kernel.php
```
protected function schedule(Schedule $schedule)
{
$schedule->command('healthcheck:api')->everyFiveMinutes();
}
```
