# Job Finder Platform

Full-stack job portal built with Laravel (API) and Vue 3 (frontend), with Dockerized backend services.

## Stack

- Backend: Laravel 12, PHP 8.4 (container), PostgreSQL 17, Sanctum auth
- Frontend: Vue 3, Vite 7, Vue Router, Axios, Tailwind CSS v4
- Infra: Docker Compose (app, nginx, postgres, pgAdmin)

## Repository Structure

- back_end/docker: Docker and service orchestration
- back_end/src: Laravel API source code
- front_end: Vue frontend application
- conception: UML diagrams
- root HTML mock pages: static pure HTML pages for quick UI preview

## Services and Ports

- Frontend (Vite): http://localhost:3000
- Backend API (Nginx): http://localhost:8000
- PostgreSQL: localhost:5432
- pgAdmin: http://localhost:5050

Default DB credentials from docker compose:

- Database: job_find_db
- User: mouad
- Password: mouad

pgAdmin login:

- Email: admin@admin.com
- Password: mouad

## Quick Start (Full Project)

### 1) Start backend containers

From back_end/docker:

```bash
docker compose up -d --build
```

### 2) Install backend dependencies (first time)

From back_end/src:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

If you run artisan inside container:

```bash
docker exec -it laravel_app php artisan key:generate
docker exec -it laravel_app php artisan migrate
```

### 3) Start frontend

From front_end:

```bash
npm install
npm run dev
```

Open the app at http://localhost:3000

## Backend Notes

Main API routes are defined in back_end/src/routes/api.php.

Public endpoints include:

- GET /api/jobs
- GET /api/companies
- GET /api/categories
- POST /api/register
- POST /api/login

Protected endpoints use Sanctum auth, including:

- GET /api/user
- GET/PUT /api/profile
- POST /api/jobs/{id}/apply
- GET /api/my-applications
- GET/PUT employer applications endpoints
- favourites endpoints
- admin overview endpoint

## Frontend Notes

Frontend route definitions are in front_end/src/router/index.js.

Main routes include:

- /
- /feed
- /jobs
- /jobs/:id/apply
- /favourites
- /my-candidatures
- /profile
- /companies
- /login
- /register
- /employer/home
- /employer/company
- /employer/jobs/create
- /employer/applications
- /admin

Dev proxy is configured in front_end/vite.config.js:

- /api is proxied to http://localhost:8000

## Static HTML Pages

Pure HTML pages are available in the root for quick previews and simple demo flows:

- home.html
- login.html
- register.html
- admin.html
- jobs.html
- feed.html
- companies.html
- profile.html
- favourites.html
- my-candidatures.html
- apply-job.html
- employer-home.html
- employer-company.html
- employer-post-job.html
- employer-applications.html

Legacy variants also present:

- dasboardAdmin.html
- loginPage.html
- registerPage.html

## Useful Commands

### Backend

```bash
# from back_end/docker
docker compose up -d --build
docker compose ps
docker compose logs -f
```

### Frontend

```bash
# from front_end
npm run dev
npm run build
npm run preview
```

## Troubleshooting

### Frontend Tailwind error: unknown utility class rounded-xl

If you hit this during Vite dev:

- Stop the frontend dev server
- Verify Tailwind plugin setup in front_end/vite.config.js
- Check component scoped style blocks for unsupported apply/reference usage
- Restart with npm run dev

### API not reachable from frontend

- Confirm backend containers are running
- Verify nginx is exposed on port 8000
- Confirm requests are sent to /api so Vite proxy can forward them

### Database connection issues

- Ensure postgres container is healthy
- Verify DB credentials in Laravel .env match docker compose values

## Documentation

- Frontend-specific documentation: front_end/README.md
- Laravel default readme: back_end/src/README.md
