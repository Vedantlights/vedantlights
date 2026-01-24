# XAMPP Setup Guide – Vedant Lights

Use this guide to run Vedant Lights on XAMPP. The following have already been applied:

- **`app/Config/App.php`** – `baseURL` set to `http://localhost/vedantlights/`
- **`.htaccess`** – `RewriteBase` set to `/vedantlights/`
- **`schema.sql`** – Database and table creation script

---

## 1. Composer dependencies

Install PHP dependencies (required for CodeIgniter 4):

1. Install Composer: https://getcomposer.org/download/
2. In a terminal, go to the project root and run:

   ```bash
   cd c:\xampp\htdocs\vedantlights
   composer install --no-dev
   ```

   This creates the `vendor/` folder. If you use `composer.phar`:

   ```bash
   php composer.phar install --no-dev
   ```

---

## 2. Database setup

1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Open **phpMyAdmin**: http://localhost/phpmyadmin
3. Import **`schema.sql`**:
   - Go to **Import** (or **SQL**).
   - Choose `schema.sql` from the project root.
   - Execute. This creates `vedantlights_db` and the tables.
4. Import **`dummy_data.sql`**:
   - Select database `vedantlights_db`.
   - Import `dummy_data.sql` to load brands, categories, products, and admin users.

---

## 3. Run the app

1. Open: **http://localhost/vedantlights/**
2. Admin login: **http://localhost/vedantlights/admin**  
   - Example users from `dummy_data.sql`: `admin` / `admin`, `vedant` / `vedant123`

---

## 4. Database configuration

Default values in `app/Config/Database.php`:

- **Host:** `localhost`
- **User:** `root`
- **Password:** *(empty)*
- **Database:** `vedantlights_db`

If your XAMPP MySQL uses different credentials, update that file or override via `.env` (see CodeIgniter docs).

---

## 5. Troubleshooting

| Issue | What to check |
|-------|----------------|
| Blank page / 500 | `writable/` must be writable; check `writable/logs/` for errors |
| 404 on routes | `mod_rewrite` enabled in Apache; `RewriteBase /vedantlights/` in `.htaccess` |
| “Composer not found” | Run `composer install` (or `php composer.phar install`) as in step 1 |
| DB connection error | MySQL running; correct host/user/pass/database; `schema.sql` imported |
| Wrong assets / redirects | `baseURL` in `app/Config/App.php` is `http://localhost/vedantlights/` |

---

## Files reference

| File | Purpose |
|------|---------|
| `schema.sql` | Creates `vedantlights_db` and tables – **run first** |
| `dummy_data.sql` | Seeds brands, categories, products, users – **run after schema** |
