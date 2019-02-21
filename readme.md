# Implement Learning Institute backend project

A project to communicate with the ICG maconomy service, so ili-wordpress can do less business logic.


# Importing from maconomy
Run this command to import from maconomy, be sure to have the maconomy URL in .env
```bash
php artisan konform:importcourses
```

On live the import service runs via the laravel scheduler & queue.
With supervisord:
```bash
php artisan queue:work --tries=1
```
With crontab every 1 minute:
```bash
php artisan schedule:run
```
For more info see: https://laravel.com/docs/5.7/scheduling
