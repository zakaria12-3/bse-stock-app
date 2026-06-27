# Render Deployment

This app is prepared for a Render Docker deployment with a Render Postgres database.

## 1. Push The Repo

Push this project to GitHub, GitLab, or Bitbucket.

## 2. Create The Render Blueprint

In Render:

1. New > Blueprint.
2. Select the repository.
3. Render reads `render.yaml`.
4. Create the web service and database.

After Render creates the service, update `APP_URL` in the service environment to the real Render URL, for example:

```text
https://bse-stock-test.onrender.com
```

## 3. Create The First Admin

Open the Render web service shell and run:

```bash
php artisan bse:create-admin --email=admin@bse.test --username=admin --password='CHANGE_THIS_PASSWORD'
```

Then send BSE:

```text
URL: https://your-render-url.onrender.com/login
Email: admin@bse.test
Password: CHANGE_THIS_PASSWORD
```

## 4. Test Checklist

- Login works.
- Import an Excel stock workbook.
- Confirm CUMP is filled.
- Create an ambulance dossier.
- Add pieces to the dossier.
- Confirm stock decreases.
- Export stock workbook.

## Notes

- Render free services can sleep after inactivity. The first request after sleep may be slow.
- Local disk storage on free Render is not a durable file archive. Excel imports are fine because they are processed immediately, but proof images should move to S3/Cloudinary before real production.
- The `/auto-login` and `/setup-admin` routes are restricted to localhost and should not be used for remote testing.
