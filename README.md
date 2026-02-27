# Padi PHP

A mini library for my personal project to implement design patterns, small utilities to have a clean archi, etc.

## Contribute to the project

### Installation

Requirements:
* docker
* make

Run:
* `make composer-install`: build docker image and install dependencies

### Code quality

We use [mago](https://mago.carthage.software/) to format, lint and analyze code.

* `make fix`: alias to run format, linter & analyze with auto fix and run linter and analyzer to show the remaining errors.
* `make lint`: alias to run linter.
* `make analyze`: alias to analyze code to detect error, etc.
* `make format`: alias to run format code.
* `make format-check`: alias to check format code.
* `make mago ARGS="--help"`: run mago with args.


### Other commands

* Run php:
    * `make php`: interactive mode.
    * `make php ARGS="-v"`: specific args.
* Run composer:
    * `make composer`: without args.
    * `make composer ARGS="help"`: specific args.
    * `make composer-install`: alias to install dependencies.
    * `make composer-dump-autoload`: alias to refresh autoload dump.
