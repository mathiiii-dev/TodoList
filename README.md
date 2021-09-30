# P8 OC DA/PHP - Symfony

Improve an existing ToDo & Co application

## Getting Started

These instructions will get you a copy of the project up and running on your local machine if you want to test it or develop something on it.

## Prerequisites

To make the project run you will need to install those things :

* [Laragon](https://laragon.org/download/)
* [PHP 8](https://www.php.net/releases/index.php)
* [Apache 2.4.35](http://archive.apache.org/dist/httpd/httpd-2.4.35.tar.gz)
* [MySQL 5.7.24](https://downloads.mysql.com/archives/get/p/23/file/mysql-5.7.24-winx64.zip)
* [Composer](https://getcomposer.org/download/)
* [Symfony](https://symfony.com/download)
* [xDebug](https://xdebug.org/docs/install)

## Installing

Follow those steps to make the project run on your machine

Clone the project :
```
git clone https://github.com/mathiiii-dev/TodoList.git
```
Install composer dependencies :
```
composer install && npm install
```
Run webpack build : 
```
npm run build
```

## Database & DataFixtures

First edit .env (or create a .env.local to override it) with your database credentials : 
```
DATABASE_URL="mysql://root:@127.0.0.1:3306/to-do-list?serverVersion=5.7"
```

Create the database :
```
php bin/console doctrine:database:create
```

Update schema :
```
php bin/console doctrine:schema:update --force
```

Load some data into the database : 
```
php bin/console doctrine:fixtures:load
```

## Test account

The datafixtures will load some users for you to test the application : 

Admin user:
```
Pseudo : Mathias
Password : password
```

Normal user :
```
Pseudo : Xavier
Password : password
```

## Launch project

Using Symfony CLI :
```
symfony serve
```

## Unit & Functional tests

The tests are available here : 
```
📦 
└─ tests
  ├─ Fixtures
  │  └─ UserTaskFixtures.yaml
  ├─ Functional
  │  └─ Controller
  │     ├─ HomeControllerTest.php
  │     ├─ SecurityControllerTest.php
  │     ├─ TaskControllerTest.php
  │     └─ UserControllerTest.php
  └─ Unit
        ├─ Entity
        │  ├─ TaskTest.php
        │  └─ UserTest.php
        ├─ Form
        │  ├─ TaskTypeTest.php
        │  └─ UserTypeTest.php
        └─ Repository
            ├─ TaskRepositoryTest.php
            └─ UserRepositoryTest.php
```

If you want to run some unit or functional tests, you can simply run : 
```
php ./vendor/bin/phunit
```

If you want to generate a code coverage report, you can run :
```
php ./vendor/bin/phunit --coverage-html path/to/directory
```

Then you can access the report here : ```127.0.0.1:8000/path/to/directory/index.html```


## Built With

* [Symfony](https://symfony.com/) - Framework PHP
* [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle) - Pagination 
* [Bootstrap](https://getbootstrap.com/) - CSS Framework
* [PHPUnit](https://github.com/sebastianbergmann/phpunit) - Unit & Functional tests

## Versioning

For the versions available, see the [tags on this repository](https://github.com/mathiiii-dev/todolist/tags). 

## Authors

* **Mathias Micheli** - *Student* - [Github](https://github.com/mathiiii-dev)
