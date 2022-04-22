<p align="center"><a href="https://www.diagro.be" target="_blank"><img src="https://diagro.be/assets/img/diagro-logo.svg" width="400"></a></p>

<p align="center">
<img src="https://img.shields.io/badge/project-lib_laravel_backend-yellowgreen" alt="Diagro backend library">
<img src="https://img.shields.io/badge/type-library-informational" alt="Diagro service">
<img src="https://img.shields.io/badge/php-8.1-blueviolet" alt="PHP">
<img src="https://img.shields.io/badge/laravel-9.0-red" alt="Laravel framework">
</p>

## Beschrijving

Deze bibliotheek wordt gebruikt als basis voor alle backends geprogrammeerd in Laravel.

## Development

* Composer: `diagro/lib_laravel_backend: "^1.1"`

## Production

* Composer: `diagro/lib_laravel_backend: "^1.1"`

## Changelog

### V1.3

* **Feature**: X-BACKEND-TOKEN validatie voor interne incoming backend calls.

### V1.2

* **Feature**: DiagroResource class

### V1.1

* **Update**: upgrade naar php8.1 en laravel 9.0

### V1.0

* **Feature**: can($abillity, $right_name) method voor Route
* **Feature**: rules per controller action functie en/of default rules.
* **Feature**: Diagro policy
* **Feature**: Diagro company voor models met een company_id kolom
* **Feature**: Middelwares voor validatie en authentications
* **Feature**: Configuratie in configs/diagro.php