# Implement Learning Institute backend project

A project to communicate with the ICG maconomy service, so ili-wordpress can do less business logic.

# API to this service
This service's purpose is to expose an API to wordpress and nuxt, so we can handle booking logic and syncs with 
various services.

I have created a full postman implementation, with different environments.
You can find them here: [Postman collection](docs/postman/ili-backend.postman_collection.json)

There are 4 environments to use:
* [Dev - stable](docs/postman/environments/Implement DEV.postman_environment.json)
* [Dev v2 - unstable: API changes a lot](docs/postman/environments/Implement DEV v2_port-85.postman_environment.json)
* [Localhost testing: sends maconomy data to DEV](docs/postman/environments/Implement LOCALHOST.postman_environment.json)
* [LIVE](docs/postman/environments/Implement LIVE.postman_environment.json)


We also have the API routes documented [here](docs/api.md)

With the API, we can also do "some" support, since we can resend emails, resync an order to maconomy or start
the maconomy sync.
All these methods are in the [Postman collection](docs/postman/ili-backend.postman_collection.json)

# Getting started

Start by copying [.env.example](.env.example) to .env on your local machine, in order to make
the site work properly. Make any adjustments needed.

To get started, just run the migrations and start a sync using the API (see 
[Postman collection](docs/postman/ili-backend.postman_collection.json))

# Deploying

We are using deployer to deploy the project.
Simply use 
```
vendor/bin/dep deploy next
```
When deploying to next server.

And use 
```
vendor/bin/dep deploy production
```
When deploying to production server.

# Cron jobs

We run cron jobs using supervisord.
This means that supervisord needs to be rebooted, if you make changes to cron jobs.

Supervisord is rebooted when you deploy, but if you are testing something on live, just remember to do it
manually using:
```
sudo systemctl restart supervisord
```

# Old stuff

We have created a rather large API that wordpress uses to sync courses

## Importing from maconomy
Run this command to import from maconomy, be sure to have the maconomy URL in .env
```bash
php artisan konform:importcourses
```

On live the import service runs via the laravel scheduler & queue.
With supervisord:
```bash
php artisan queue:work --tries=1
```


## How do i fix it?!
ilibackend authentication error if you just copied database from dev fx:
401/400 error in ilibackend api.
Problem: Your token is valid, but not for localhost usage
Fixing it: Generate a new.
 Find method: getToken - remove the check for current valid token, and generate a new by commenting it away.
