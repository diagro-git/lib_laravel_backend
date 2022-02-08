<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://diagro.be/assets/img/diagro-logo.svg" width="400"></a></p>

<p align="center">
<img src="https://img.shields.io/badge/library-laravel_backend-yellowgreen" alt="Diagro backend library">
<a href="https://github.com/diagro-git/service_auth"><img src="https://img.shields.io/badge/type-library-informational" alt="Diagro service"></a>
<a href="https://php.net"><img src="https://img.shields.io/badge/php-8.0-blueviolet" alt="PHP"></a>
<a href="https://laravel.com/docs/8.x/"><img src="https://img.shields.io/badge/laravel-8.67-red" alt="Laravel framework"></a>
</p>

## Beschrijving

Deze bibliotheek wordt gebruikt als basis voor alle backends geprogrammeerd in Laravel.

## Development

* Composer: `diagro/lib_laravel_backend: "dev-development"`

## Production

* Composer: `diagro/lib_laravel_backend: "1.0.0"`

## Changelog

### V1.0.0

* **Feature**: can($abillity, $right_name) method voor Route
* **Feature**: rules per controller action functie en/of default rules.
* **Feature**: Diagro policy
* **Feature**: Diagro company voor models met een company_id kolom
* **Feature**: Middelwares voor validatie en authentications
* **Feature**: Configuratie in configs/diagro.php