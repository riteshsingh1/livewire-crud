# LiveCrud


![Packagist License](https://img.shields.io/packagist/l/imritesh/livecrud)
![Packagist Downloads](https://img.shields.io/packagist/dt/imritesh/livecrud)
![Packagist Version](https://img.shields.io/packagist/v/imritesh/livewire)




Live Crud Generator. This package generates Basic Crud with Livewire.

![](./livewire-crud.gif)

## Features
 - Generate Complete Crud With Livewire Component and Blade Files
 - Create / Update / Delete Functional
 - Real Time Validation Already Added
 - Fuzzy Search Functional

## Installation

Via Composer

``` bash
composer require imritesh/livecrud
```

## Prerequisites
- Models should be in `app/Models`  directory
- Crud of only $fillable property will be generated 
```php 
protected $fillable = ['name','username'];
``` 

## Usage
```bash
php artisan crud:make Name_Of_Your_Model
```

- This Command Will Generate Two Files
    - First Will be in `app/HttpLivewire`
    - Second Will be in `resources/views/Livewire`





## Limitations
- This Packages uses [Tailwind CSS V2](https://tailwindcss.com/)

## TODO
- Bootstrap 4 & 5 Support
- Bulma Support


## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [Ritesh Singh](https://imritesh.com)

## License

license. Please see the [license file](https://github.com/riteshsingh1/livewire-crud/blob/master/license.md) for more information.
