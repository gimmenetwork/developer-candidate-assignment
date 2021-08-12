#Setup

run `docker-compose up -d`

run `composer install` on your machine

migrations:

- run `docker ps` and find PHP image
- run `docker exec -it <container_id> vendor/bin/doctrine-migrations migrate`
- press Enter

database root: root:123root

go to `localhost/`

VOILA!

##tests

- run `docker ps` and find PHP image
- run `docker exec -it <container_id> composer test`

# Users

There is already an admin user

admin:123321

He can add/remove/edit books

## New users

Go to sign up page and create an account

## Leasing

To lease or return book you should log in

## Book manipulations

To add/edit/remove a book log in as `admin` with pass `123321`

## Search api

I hadn't time to include it into the frontend. So you can test it with a tool like Postman.

`GET localhost/api/search?author=2&genre=5` you may provide not both but one only

response
```
{
    "books": [
        {
            "id": int,
            "title": string,
            "year": int,
            "count_in_stock": int,
            "total_count": int,
            "available_for_leasing": bool // AKA soft delete
        }
    ]
}
```