Gimmemore task

Description
-----------------
The scope of this task is to create a CRUD interface which handles books and readers.
The implementation is made in both sides, backend and frontend.

**BACKEND**

In the backend area we have:
1. Book - defines the book
2. Genre - defines the genre of a book
3. Reader - defines a Reader . A Reader can lease books

The controllers handle the API requests helped by the implementation of FosRestBundle .
We also have specific Reader and Book entity managers  who handle the database writes . 
The `BookManager` and `ReaderManager` extends the implementation from `EntityManagerHandler`. This is made for a little of abstraction.

**Filtering**

The filtering is made by sending proper key-value pairs from frontend and is handled by `FilterManager`.
The `FilterManager` requires `ParamFetcher` to read filters send through XHttp request and iterates through each filter handler to apply the filters to the `QueryBuilder` .
For now we only have one single filter handler, `BookFilter` . You'll notice that the filter handler implements a specific interface `FilterHandlerInterface`.
This's happening to be able to load automatically all filter handlers in filter manager.

**FRONTEND**

The frontend side is developed using React JS and some grid specific components from material-ui https://material-ui.com .
The datagrid side is abstract . I managed to develop an abstract idea, meaning each datagrid is build/loaded due to a datagrid configuration object.
The datagrid configuration is in `assets/js/datagridDefinition.js` .
The CrudComponent handles the datagrid loading and actions.

Techs used
------------
1. Symfony framework version 5.3
2. FosRestBundle - to handle the API request
3. Node and React - to handle the frontend side
4. Material-ui to handle the design and components used by frontend
5. PHPUnit for unit testing
6. I also managed to write an installation command `InstallProjectCommand` whose name is `gimmemore:install` to be easy to install the project


Requirements
------------
1. PHP 7.4
2. MySQL 5.7
4. Node 14.15.0
3. Composer - you can find installation help at https://getcomposer.org/download/

Installation
------------

1. unzip the project
2. copy the `.env.prod.local.sample` to `.env.prod.local`
3. setup the DATABASE_URL value
4. for development purpose change in .app file the ``APP_ENV`` constant to ``dev``
5. make sure you have Node.js - https://nodejs.org/en/download/ - and Yarn package manager installed - https://yarnpkg.com/getting-started/install
6. run ``composer install`` to install the vendors/libraries
8. run ``bin/console gimmemore:install`` to install the project including node packages and the fixtures needed for testing the implementation 
