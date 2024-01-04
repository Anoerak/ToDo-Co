<h1 align="center">Welcome to ToDo & Co 👋</h1>
<p>
  <a href="#" target="_blank">
    <img alt="License: MIT" src="https://img.shields.io/badge/License-MIT-yellow.svg" />
  </a>
</p>

> This project goal is to upgrade the original ToDo & Co Symfony App from v3 to V6. You'll find the original project by following the link
> above:

-   ToDo & Co: [Original Repo](https://github.com/saro0h/projet8-TodoList)

## Install

```sh
Install dependencies with `composer install` and `npm install`
```

## Usage

```sh
Update the .env file with your database credentials
```

```sh
Run the command `symfony console doctrine:database:create` to create the database
```

```sh
Run the command `symfony console doctrine:schema:update --force` to create the tables
```

```sh
Run the command `symfony console doctrine:fixtures:load` to load the fixtures
```

```sh
Run the command `symfony serve` to start the server
```

```sh
Run the command `npm run dev-server` to start the webpack server
```

```sh
Run the command `npm run watch` to start the webpack watcher
```

## Run tests

```sh
Run the command `symfony php bin/phpunit` to run the tests
```

## Run tests with coverage

```sh
Run the command `symfony php bin/phpunit --coverage-html public/coverage` to run the tests with coverage
```

## Check code quality

With phpstan:

```sh
Run the command `symfony php vendor/bin/phpstan analyse src --level 6` to check the code quality
```

With php-cs:

```sh
Run the command `symfony php vendor/bin/phpcs src --standard=PSR12 -p` to check the code quality
```

## Documentations

A documentation (generated with phpDocumentor) folder (public/support/docs/Technical Doc) is available in the public folder. You can access
it by opening the 'index.html' file in your browser.

An audit report (using the Symfony profiler) is also available in the this folder (public/support/audit). A documentation explaining the
authentication process is available in the public folder (public/support/docs/Authentication). A documentation explaining how to contribute
the project is also available in the public folder.

-   [Documentation](public/support/docs/Technical%20Doc/index.html)
-   [Audit](public/support/audit/audit.md)
-   [Authentication](public/support/docs/Authentication/authentication.md)
-   [Contribution](public/support/docs/Contribution/contribution.md)

## Technologies

-   [Symfony](https://symfony.com/doc/current/index.html)
-   [SymfonyUX](https://ux.symfony.com/)
-   [Doctrine](https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/index.html)
-   [Twig](https://twig.symfony.com/doc/3.x/)
-   [PHPUnit](https://phpunit.readthedocs.io/en/9.5/)
-   [Webpack](https://webpack.js.org/concepts/)
-   [React](https://reactjs.org/docs/getting-started.html)
-   [phpstan](https://phpstan.org/)
-   [php-cs](https://github.com/squizlabs/PHP_CodeSniffer/wiki)

## Author

👤 **Anørak**

-   Github: [@Anørak](https://github.com/Anørak)

## 🤝 Contributing

Contributions, issues and feature requests are welcome!<br />Feel free to check [issues page](https://github.com/Anoerak/ToDo-Co/issues).

## Show your support

Give a ⭐️ if this project helped you!

---

_This README was generated with ❤️ by [readme-md-generator](https://github.com/kefranabg/readme-md-generator)_

Update Tasks -> New user (anonymous) -> update table with Migration + update with command line (both).
