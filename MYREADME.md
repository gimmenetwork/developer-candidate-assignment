# Library Rest API

Symfony 5 + PHP8 + DDD + Docker + JSON responses 


## Table of Contents
+ [About](#about)
+ [Test](#test)
+ [Considerations](#considerations)



## About <a name = "about"></a>
Symfony 5 REST API Library APP, inclusive of:

- *JWT* (lexik/jwt-authentication-bundle) for authentication
- *DDD* Domain Driven Design
- *Docker* to set up DB and run the app
- *Doctrine*

### Prerequisites

What things you need to install the software and how to install them.
- PHP 7.8+
- composer
- symfony
- docker

### Installing

```bash
composer install
make init
```

### Running the App

#### Install database
```bash
make schema-update
make load-fixtures
```

#### Import file to test endpoints with Postman

[library-api.postman_collection.json](docs/postman/library-api.postman_collection.json)

## Tests <a name = "test"></a>

### Running PHPUnit Tests

#### Install test database
```bash
make schema-create-test
make load-fixtures-test
```
#### Run tests
```bash
bin/phpunit
```

#### Code Coverage
```bash
make code-coverage
```

## Considerations <a name = "considerations"></a>

#### Improvements
- Add validation for request parameters
- Use (de)serialization to handle request parameters and response data
- Log exeption messages
- Add api version
- Add UI
