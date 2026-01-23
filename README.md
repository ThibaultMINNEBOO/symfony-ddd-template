# Symfony Domain Driven Development - Boiler plate

This repository provides a boilerplate for building applications using Symfony with a focus on Domain-Driven Design (DDD) principles. It includes a structured folder layout, essential configurations, and examples to help you get started quickly.

## Features

- Structured folder layout following DDD principles
- Pre-configured Symfony setup
- Authentication and authorization already implemented
- Integration with Doctrine ORM

## Getting Started

You can get started by cloning the repository and installing the dependencies:

```bash
git clone https://github.com/Horyzone/symfony-ddd.git
cd symfony-ddd
composer install
```

## Folder Structure

The project is organized into the following main directories:

- `src/Domain`: Contains the core domain logic, including entities, value objects, and domain services.
- `src/Application`: Contains application services that orchestrate domain logic.
- `src/Infrastructure`: Contains implementations for repositories, external services, and other infrastructure concerns.
- `src/Presentation`: Contains controllers, views, and other presentation layer components.


