# PHP CRUD

A simple users management app written in plain PHP with a MySQL database. You can add, view, update and delete user records from a basic table.

## Features

- List all users on the home page
- Create a new user
- View a single user's details
- Update an existing user
- Delete a user (with redirect back to the list)

Note: passwords are stored as plain text in the database. This is a learning/demo project, so treat it as such — don't use it with real accounts.

## Folder Structure

```
crud_php/
├── index.php              # redirects to includes/home.php
├── db.php                 # database connection settings
├── includes/
│   ├── home.php           # user list page
│   ├── create.php         # add user form
│   ├── update.php         # edit user form
│   ├── view.php           # single user detail view
│   ├── delete.php         # handles user deletion
│   └── style.css          # page styles
└── Database File/
    └── php_crud.sql       # database dump (db + users table)
```

## Setup

1. Import the database: run `Database File/php_crud.sql` in phpMyAdmin (or your DB tool of choice). It creates the `php_crud` database and the `users` table with a few sample rows.
2. Update `db.php` if your MySQL credentials are different (defaults are localhost, user `root`, no password).
3. Put the folder inside your web root (e.g. `htdocs`) and open it in the browser (e.g. `http://localhost/crud_php/`).

## Requirements

- PHP 5 or newer
- MySQL / MariaDB
- A web server (Apache, or PHP's built-in server works too)
```