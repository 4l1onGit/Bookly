# 📚 Bookly – Full‑Stack Symfony Web App & REST API #

Bookly is a full‑stack web application built with Symfony, featuring a complete REST API, JWT authentication, external API integrations, and a Twig‑based frontend.
Originally developed as a university project, it has since been refined into a production‑style application demonstrating real‑world backend architecture and modern PHP development practices.

[Live Link](http://bookly-api-b82d6a0cb81b.herokuapp.com/)

[WIP Next.js Frontend](https://github.com/4l1onGit/bookly-next-frontend)

# Features #

## 🔐 Authentication & Authorization ##

    JWT‑based authentication (LexikJWTAuthenticationBundle)

    Role‑based access control (User, Admin, Moderator)

    Protected API routes

    Review editing/deletion restricted to authors or privileged roles

## 📘 Books ##

    Create, update, delete, and view books

    REST API endpoints for all CRUD operations

    Form validation using Symfony Forms

    Database persistence via Doctrine ORM

## ⭐ Reviews ## 

    Nested REST routes: `/books/{id}/reviews`

    Users can create, update, and delete their own reviews

    Admins/Moderators can manage all reviews

    Validation and error handling included

## 🌐 External API Integrations ##

    Google Books API

    OpenLibrary API

    Search and import book data from external sources

## 🎨 Frontend ##

    Twig templates for all UI pages

    Clean layout with reusable components (nav, footer, base layout)

    Pages for:

        Book management

        Review management

        Library browsing

        ISBN lookup

        User registration & login

## 🗄️ Database & Migrations ##

    Doctrine ORM entities for Books, Reviews, Users, and Roles

    Fully versioned migrations

## Tech Stack ##

| Layer | Technology |
|-------|------------|
| Backend | Symfony 6, PHP 8 |
| API | FOSRestBundle, JMS Serializer, LexikJWTAuthenticationBundle |
| Database | Doctrine ORM, MySQL/PostgreSQL |
| Frontend | Twig templating engine |
| External APIs | Google Books, OpenLibrary |
| Tools | Symfony CLI, Composer |



## REST API Overview ##

A full Swagger/OpenAPI documentation page will be added soon.

### Books ###

```
GET    /api/v1/books
GET    /api/v1/books/{id}
POST   /api/v1/books
PUT    /api/v1/books/{id}
DELETE /api/v1/books/{id}

```

### Reviews (Nested) ###

```
GET    /api/v1/books/{id}/reviews
GET    /api/v1/books/{id}/reviews/{reviewId}
POST   /api/v1/books/{id}/reviews
PUT    /api/v1/books/{id}/reviews/{reviewId}
DELETE /api/v1/books/{id}/reviews/{reviewId}

```
## Project Structure ##

```

src/
 ├── Controller/
 ├── Entity/
 ├── Repository/
 ├── Form/
 └── Kernel.php
templates/
config/
migrations/
public/

```
    
