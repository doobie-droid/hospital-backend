<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel was used to build this repo. Google it and see how lovely it is.... It is accessible, powerful, and provides tools required for large, robust applications.

## SETUP PROJECT FOR LOCAL DEVELOPMENT

Make sure you install [docker](https://docs.docker.com/engine/install/ubuntu/) and you can watch the [video](https://www.youtube.com/watch?v=2ezNqqaSjq8&t=488s) so that you install docker properly on your ubuntu wsl. If it crucial you watch and install the right way or your requests would take forever to send

### Premium Partners

After that, run the following commands in this order: <br>
To set up your containers from the docker files/instructions there, run

```bash
$ docker-compose -f docker-compose.yml up -d --build
```

Afterwards run the next line to install any dependencies you may be missing, It is not needed now
but in the future, I could add some packages

```bash
$ docker-compose -f docker-compose.yml exec app composer install --ignore-platform-reqs
```

Lastly, run this to perform your database migrations

```bash
$ docker-compose -f docker-compose.yml exec app php artisan migrate:fresh

```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
