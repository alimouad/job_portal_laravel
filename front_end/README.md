# Job Finder Platform - Frontend

Vue 3 frontend for the Job Finder Platform, built with Vite and Tailwind CSS v4.

This app includes:
- Public pages (home, jobs, companies, auth)
- Job seeker pages (profile, favourites, candidatures, apply)
- Employer pages (company profile, post jobs, applications)
- Admin dashboard page

## Tech Stack

- Vue 3
- Vue Router
- Axios
- Tailwind CSS v4 via @tailwindcss/vite
- Vite

## Requirements

- Node.js: ^20.19.0 or >=22.12.0
- npm
- Backend API running locally (Laravel)

## Install

```bash
npm install
```

## Run in Development

```bash
npm run dev
```

Frontend runs on:
- http://localhost:3000

API proxy is configured in [vite.config.js](vite.config.js):
- /api -> http://localhost:8000

## Build for Production

```bash
npm run build
```

## Preview Production Build

```bash
npm run preview
```

## Project Structure

```text
front_end/
  src/
    assets/
    components/
    layouts/
    pages/
      admin/
      auth/
      employer/
      user/
    router/
    axios.js
    main.js
```

## Frontend Routes

Defined in [src/router/index.js](src/router/index.js):

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

Admin route protection:
- Checks auth token from localStorage
- Calls /user to verify role = admin
- Redirects to login or home when unauthorized

## API Notes

- Axios client is configured in [src/axios.js](src/axios.js)
- Dev server proxy forwards /api requests to backend
- Backend expected at http://localhost:8000

## Static HTML Pages (Generated)

Pure HTML mock pages exist in the project root and can be used for quick UI preview:

- [../home.html](../home.html)
- [../login.html](../login.html)
- [../register.html](../register.html)
- [../admin.html](../admin.html)
- [../feed.html](../feed.html)
- [../jobs.html](../jobs.html)
- [../apply-job.html](../apply-job.html)
- [../favourites.html](../favourites.html)
- [../my-candidatures.html](../my-candidatures.html)
- [../profile.html](../profile.html)
- [../companies.html](../companies.html)
- [../employer-home.html](../employer-home.html)
- [../employer-company.html](../employer-company.html)
- [../employer-post-job.html](../employer-post-job.html)
- [../employer-applications.html](../employer-applications.html)

## Common Commands

```bash
# start dev server
npm run dev

# production build
npm run build

# preview build
npm run preview
```

## Troubleshooting

If you see Tailwind utility errors during dev:

1. Stop dev server
2. Ensure dependencies are installed
3. Restart with npm run dev
4. Verify Tailwind plugin setup in [vite.config.js](vite.config.js)

If API calls fail:

1. Confirm backend is running at http://localhost:8000
2. Check proxy in [vite.config.js](vite.config.js)
3. Inspect request base URL in [src/axios.js](src/axios.js)
