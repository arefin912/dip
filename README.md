# TMS Website

This is a PHP-based transport management system located in the repository root.

## Deployment

This repository is prepared for Docker-based deployment, which works on both Render and Vercel.

### Environment variables

Create a `.env` file locally or configure the platform with these values:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=tms_database
```

### Render

Render can deploy this repository using the provided `Dockerfile`.

1. Push the repository to GitHub.
2. Create a new Web Service on Render.
3. Select "Docker" as the environment.
4. Use the default `Dockerfile` and point the root to this repository.
5. Add environment variables in Render:
   - `DB_HOST`
   - `DB_USER`
   - `DB_PASS`
   - `DB_NAME`
6. Deploy.

### Vercel

Vercel can deploy using the `vercel.json` file and Docker support.

1. Push the repository to GitHub.
2. Import the project on Vercel.
3. Set the project to use the `Dockerfile` builder.
4. Add environment variables in the Vercel dashboard:
   - `DB_HOST`
   - `DB_USER`
   - `DB_PASS`
   - `DB_NAME`
5. Deploy.

### Local development

Build and run locally with Docker:

```bash
docker build -t tms-website .
docker run --rm -p 8080:80 \
  -e DB_HOST=localhost \
  -e DB_USER=root \
  -e DB_PASS= \
  -e DB_NAME=tms_database \
  tms-website
```

Then visit `http://localhost:8080`.

## Notes

- The app files are in the repository root.
- `includes/db_config.php` now reads database settings from environment variables.
- Import `tms_database.sql` into your MySQL/MariaDB database before using the app.
