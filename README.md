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
