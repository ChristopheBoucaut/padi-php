# Padi PHP

A mini library for my personal project to implement design patterns, small utilities to have a clean archi, etc.

## Contribute to the project

### Installation

Requirements:
* docker
* make

Run:
* `make composer-install`: build docker image and install dependencies

### Commands

* Run php:
    * `make php`: interactive mode.
    * `make php ARGS="-v"`: specific args.
* Run composer:
    * `make composer`: without args.
    * `make composer ARGS="help"`: specific args.
    * `make composer-install`: alias to install dependencies.
    * `make composer-dump-autoload`: alias to refresh autoload dump.
