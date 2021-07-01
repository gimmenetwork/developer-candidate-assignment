# Library CRUD

## Documentation
Bootstrapping the project took much more time than what I have expected. At the beginning I made one entity `Person` and wanted to use it for readers and authors, since an author can be a reader as well, but I have faced some issues so I went for two entities `Reader` and `Author`   

Another topic that took me more time is listing books with availability. I didn't find an easy way to implement it since doctrine doesn't support OUTER JOINs so went for plain SQL query which doesn't work if no lease record exists i.e. bug.

The most thing took my time, was trying to create a UI by using react. Symfony supports react well but setting up the dependencies didn't work for me and rendering was breaking, so I removed most of it.

Unfortunately I lost most of the time with these points so I didn't solve the main requested points like searching.

Only one test case is added as an example.

The used docker image is meant only for development. For production, it has to be optimised.

I also had to skip handling exceptions although I had that in mind.

Since I am traveling from 2nd to 5th July I thought it doesn't make sense to ask for postponing the deadline. So I decided to submit anyway.

Thanks a lot =)


## Running the project with docker
to run the project for the first time run
```shell
$ docker-compose up -d
```

For stopping and starting the container use
```shell
$ docker-compose start
$ docker-compose stop
```

Project will be available at http://localhost:8080

Database
```
host:localhost
port: 33061
user: api
password: 123
```

For running migrations:
```shell
docker-compose exec app php /app/bin/console doctrine:migrations:migrate
```

## Endpoints

### GET /books

retrieves ONLY available books

#### Request
> filters are not implemented

#### Response

```json
[
  {
    "id": 2,
    "title": "Hamlet",
    "genre": "tragedy",
    "authors": [
      {
        "id": 1,
        "name": "William Shakespeare"
      }
    ]
  },
  {
    "id": 3,
    "title": "Othello",
    "genre": "tragedy",
    "authors": [
      {
        "id": 1,
        "name": "William Shakespeare"
      }
    ]
  },
  {
    "id": 4,
    "title": "Wuthering Heights",
    "genre": "novel",
    "authors": [
      {
        "id": 2,
        "name": "Ernest Hemingway"
      }
    ]
  }
]
```

### POST /books

create an author

#### Request
```json
{
  "title": "Hamlet",
  "genre": "tragedy",
  "author_ids": [
    1
  ]
}
```

#### Response

```json
{
  "success": true
}
```

### DELETE /books/{id}

#### Response

```json
{
  "success": true
}
```

### POST /books/{bookId}/lease
to lease an existing book to an existing reader

#### Request
```json
{
    "reader_id": 1,
    "return_at": "2021-08-01"
}
```

#### Response
```json
{
    "success": true
}
```

<hr>

### GET /authors

gets all authors

#### Request
>filter are not implemented

#### Response

```json
[
  {
    "id": 1,
    "name": "William Shakespeare"
  },
  {
    "id": 2,
    "name": "Ernest Hemingway"
  }
]
```


### POST /authors

create an author

#### Request
```json
{
    "name": "William Shakespeare"
}
```

#### Response

```json
{
  "success": true
}
```

## Running Tests

```shell
$ docker-compose start
$ docker-compose exec app composer install --dev
$ docker-compose exec app php /app/bin/phpunit -d /app/tests/Factory
```