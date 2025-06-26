# Goodwin Web Project Management System

## Demo

(database is seeded with fake data, so when we try to open fake file it gives error, because it doesn't exist)

https://github.com/user-attachments/assets/edc93146-3242-47a2-8bc2-d4c2358b9eda

## Setup & Run Guide

## Prerequisites

-   **Docker** and **Docker Compose** ([Install Docker](https://docs.docker.com/get-docker/))
-   **Git**
-   (Optional) **Node.js** (not required if using Sail for npm scripts)

---

## 1. Clone the Repository

```bash
git clone <your-repo-url>
cd goodwin-web
```

---

## 2. Environment Setup

### Copy the Example Environment File

```bash
cp .env.example .env
```

### Generate the Application Key

```bash
./vendor/bin/sail artisan key:generate
```

> If you haven't installed dependencies yet, do so in the next step first.

---

## 3. Install Dependencies

### Install Composer Dependencies

```bash
composer install
```

### Install NPM Dependencies

```bash
npm install
```

---

## 4. Start Laravel Sail (MySQL & Adminer)

### Linux / Mac

```bash
./vendor/bin/sail up -d
```

### Windows (WSL2)

```bash
vendor\bin\sail up -d
```

---

## 5. Database Setup

### Run Migrations & Seeders

```bash
./vendor/bin/sail artisan migrate --seed
```

---

## 6. Frontend (Vite) Dev Server

In a new terminal (with Sail running):

```bash
./vendor/bin/sail npm run dev
```

Or, for production build:

```bash
./vendor/bin/sail npm run build
```

---

## 7. Access the App

-   **Web App:** [http://localhost](http://localhost)
-   **Adminer (DB Management):** [http://localhost:8080](http://localhost:8080)

---

## 8. Login Credentials

-   **Test User:**  
    Email: `valves@goodwingroup.com`  
    Password: `12345678`

-   **Adminer (Database Management):**

    -   **System:** MySQL
    -   **Server:** mysql
    -   **Username:** sail
    -   **Password:** password
    -   **Database:** sail

    > These values match the defaults in your `.env` and `docker-compose.yml`. If you change them, use your custom values.

---

## 9. Running Tests

```bash
./vendor/bin/sail test
```

---

## 10. Troubleshooting & Tips

-   If you get a "command not found" error for `sail`, use `bash vendor/bin/sail ...` or `vendor\bin\sail ...` on Windows.
-   If ports 80 or 8080 are in use, change them in `docker-compose.yml` and `.env`.
-   To stop all containers:  
    `./vendor/bin/sail down`
-   To rebuild containers after dependency changes:  
    `./vendor/bin/sail build --no-cache`
-   If you need to reset the database:  
    `./vendor/bin/sail artisan migrate:fresh --seed`

---

## 11. .env File Example

Make sure your `.env` contains (or matches) the following for Sail:

```
APP_NAME=GoodwinWeb
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=sail
DB_USERNAME=sail
DB_PASSWORD=password

VITE_PORT=5173
```

---

## 12. Additional Notes

-   **Node.js** is not required on your host if you use Sail for npm scripts.
-   **Adminer** is included for easy database management.
-   **All commands** prefixed with `./vendor/bin/sail` run inside the Docker container.

---

## 13. Support

If you encounter issues, check:

-   Docker is running
-   Ports 80, 8080, and 5173 are available
-   Your `.env` matches the above settings

---

**That's all!**
