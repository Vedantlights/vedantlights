# Backend Reference for React Frontend Migration

This document lists **all backend information you need** when replacing the frontend with React, with **minimal backend changes**. The backend is **CodeIgniter 4** (PHP), MySQL, and currently server-renders HTML.

---

## 1. Base URL & Environment

| Item | Value |
|------|--------|
| **Base URL** | `https://www.vedantlights.com/` (from `app/Config/App.php`) |
| **Index page** | Empty (clean URLs, no `index.php` in paths) |
| **URL rewrite** | `.htaccess` forwards non-file requests to `index.php` |

**React:** Use this as `VITE_APP_API_BASE` or `REACT_APP_API_BASE` (or equivalent). All API calls: `baseURL + path` (e.g. `https://www.vedantlights.com/sendmail`).

---

## 2. Recommended Request Headers (React)

Use these for API calls so the backend returns JSON where applicable:

| Header | Value | When |
|--------|--------|------|
| `Accept` | `application/json` | All API calls |
| `Content-Type` | `application/json` | When sending JSON body |
| `Content-Type` | `application/x-www-form-urlencoded` or `multipart/form-data` | Form / file uploads |
| `X-Requested-With` | `XMLHttpRequest` | Contact, login, CategoryDetails (isAJAX checks) |

For credentialed (admin) requests, use **`credentials: 'include'`** in `fetch()` so the session cookie is sent.

---

## 3. Strategy: Minimize Backend Changes

**Current behavior:** Controllers return **HTML views**. There is **no REST API**; some endpoints return **JSON** only when used via AJAX (e.g. login, contact, categories-by-brand).

**Recommended approach for React:**

1. **Add thin API routes** that return **JSON only**, reusing existing **Models** and **business logic**.
2. **Keep** `Home`, `Brand`, `Category`, `Product`, `Login` logic; **add** e.g. `Api\Home`, `Api\Brand`, etc. (or methods like `apiBrandDetails`) that return `$this->response->setJSON(...)`.
3. **Keep** session-based auth for admin. React can use **same-origin** requests with `credentials: 'include'` so the `ci_session` cookie is sent. If React runs on another origin, you’ll need **CORS** and possibly **cookie** `SameSite`/`Secure` adjustments.

**What to avoid:** Changing existing POST endpoints to always return JSON and break current server-rendered forms, unless you fully remove the old UI.

---

## 4. Routes Reference

### 4.1 Explicit routes (`app/Config/Routes.php`)

| Method | Path | Controller::Method | Purpose |
|--------|------|--------------------|---------|
| GET | `/` | `Home::index` | Home page |
| GET | `/contactus` | `Home::contactus` | Contact page |
| **POST** | **`/sendmail`** | **`Home::sendmail`** | **Contact form submit** |
| GET | `/aboutus` | `Home::aboutus` | About page |
| GET | `/brandDetails/(:any)/(:any)` | `Home::brandDetails/$1/$2` | Brand page (brandId, brandName) |
| GET | `/productDetails/(:any)` | `Home::productDetails/$1` | Product detail (proId) |
| GET | `/categoryDetails/(:any)/(:any)/(:any)` | `Home::categoryDetails/$1/$2/$3` | Category page (brandId, brandName, catId) |
| GET | `/categorywiseProductdetails/(:any)/(:any)` | `Home::categorywiseProductdetails/$1/$2` | *(Route exists; implementation not in current Home controller—verify before use)* |
| GET | `/admin` | `Login::index` | Admin login / dashboard |
| GET | `/dashboard` | `Login::dashboard` | Dashboard |
| **POST** | **`/addBranddetails`** | **`Brand::addBranddetails`** | **Add brand** |
| GET | `/editBrand/(:any)` | `Brand::editBrand/$1` | Edit brand form (brandId base64) |
| **POST** | **`/updateBranddetails`** | **`Brand::updateBranddetails`** | **Update brand** |
| **POST** | **`/addCategorydetails`** | **`Category::addCategorydetails`** | **Add category** |
| GET | `/editcategory/(:any)` | `Category::editcategory/$1` | Edit category form (catId base64) |
| **POST** | **`/updateCategorydetails`** | **`Category::updateCategorydetails`** | **Update category** |
| **POST** | **`/addproductdetails`** | **`Product::addproductdetails`** | **Add product** |
| GET | `/ProductDetails` | `Product::ProductDetails` | Product list (admin) |
| GET | `/editProductDetails/(:any)` | `Product::editProductDetails/$1` | Edit product form (proId base64) |

### 4.2 Auto-routed (no entry in `Routes.php`)

**Auto-routing is ON** (`app/Config/Routing.php` → `autoRoute = true`). Controllers live in `App\Controllers`, so `Controller::method` maps to `/Controller/method`:

| Method | Path | Controller::Method | Purpose |
|--------|------|--------------------|---------|
| GET | `/Brand` | `Brand::index` | Brand list + add form |
| GET | `/Category` | `Category::index` | Category list + add form |
| GET | `/Product` | `Product::index` | Product “add” page |
| **POST** | **`/Login/check_login`** | **`Login::check_login`** | **Admin login (JSON)** |
| **POST** | **`/Product/CategoryDetails`** | **`Product::CategoryDetails`** | **Categories by brand (JSON)** |
| **POST** | **`/Product/updateProductdetails`** | **`Product::updateProductdetails`** | **Update product** |
| GET | `/Product/deleteproduct/(:any)` | `Product::deleteproduct/$1` | Delete product (id base64) |
| GET | `/Category/deletecategory/(:any)` | `Category::deletecategory/$1` | Delete category (id base64) |

---

## 5. Data You Need for React (Public Site)

### 5.1 Home page

- **Brands** for nav: `HomeModel::BrandDetails()`  
  - Table: `brand_details`, `is_delete = 0`, order `brand_id DESC`.

### 5.2 Brand details page

- **URL params:** `brandId`, `brandName`.
- **Data:**
  - `brandDetails` – all active brands (same as home).
  - `catDetails` – categories for this brand: `HomeModel::getCategoryDetails($brandId)`.
  - `proDetails` – products for brand + category.  
    - Brand page uses **first category** in list.  
    - Category page uses **selected** `cat_id`.

### 5.3 Category details page

- **URL params:** `brandId`, `brandName`, `cat_id`.
- Same structure as brand details; `proDetails` filtered by `cat_id`.

### 5.4 Product details page

- **URL param:** `proId`.
- **Data:** `HomeModel::getproductDetails($proId)` → single product (object).

### 5.5 Contact form

- **Endpoint:** `POST /sendmail`
- **Body (form-urlencoded or JSON):** `name`, `email`, `subject`, `message`.
  - Required: `name`, `email`, `message`. `subject` optional (default `"No Subject"`).
- **JSON response:** Only when the backend treats the request as AJAX (`$request->isAJAX()`). CodeIgniter typically checks the `X-Requested-With: XMLHttpRequest` header.
- **React:** Send `X-Requested-With: XMLHttpRequest` (and optionally `Accept: application/json`) so the backend returns JSON instead of redirecting.
  - Success: `{ "status": "success", "message": "Thank you! Your message has been sent successfully." }`
  - Error: `{ "status": "error", "message": "..." }`
- **Email:** Sent to `sudhakarpoul@vedantlights.com` (in `Home::sendmail` and `app/Config/Email.php`).

---

## 6. Admin APIs (JSON / Forms)

### 6.1 Login

| Item | Value |
|------|--------|
| **Endpoint** | `POST /Login/check_login` |
| **Body** | `user_name`, `password` (form or JSON) |
| **Response** | JSON: `{ "txt_code": 101, "message": "Login Successfully." }` or `{ "txt_code": 102, "message": "Invalid Username And Password." }` |
| **Auth** | On success, session keys: `mst_id`, `username`, `admin_loggedin = '1'`. Session cookie: `ci_session`. |

**React:** Use `credentials: 'include'` for cookie-based auth. Send `X-Requested-With: XMLHttpRequest` if the backend checks `isAJAX()`. Check `txt_code === 101` for success.

### 6.2 Categories by brand (for dropdowns)

| Item | Value |
|------|--------|
| **Endpoint** | `POST /Product/CategoryDetails` |
| **Body** | `brandId` (brand id) |
| **Response** | `{ "StatusCode": 101, "categoryDetails": [ { "cat_id", "caterogyName", ... } ] }` or `StatusCode: 102` and empty list |
| **Auth** | Admin only (session). Unauthenticated → login HTML. |

**Note:** Category name in DB is **`caterogyName`** (typo), not `categoryName`.

### 6.3 Brands

| Action | Method | Endpoint | Body | Response |
|--------|--------|----------|------|----------|
| List | GET | `/Brand` | — | HTML. For API, you’d need new JSON endpoint. |
| Add | POST | `/addBranddetails` | `brand_name` | Redirect + flash. For API, add JSON response. |
| Edit form | GET | `/editBrand/{base64(brand_id)}` | — | HTML. |
| Update | POST | `/updateBranddetails` | `brand_name`, `brandId` | Redirect + flash. |

**Validation:** Duplicate `brand_name` → error. No “exclude current” logic when updating.

### 6.4 Categories

| Action | Method | Endpoint | Body | Response |
|--------|--------|----------|------|----------|
| List | GET | `/Category` | — | HTML. |
| Add | POST | `/addCategorydetails` | `brand_name` (brand id), `category_name` | Redirect + flash. |
| Edit form | GET | `/editcategory/{base64(cat_id)}` | — | HTML. |
| Update | POST | `/updateCategorydetails` | `brand_name`, `category_name`, `catId` | Redirect + flash. |
| Delete | GET | `/Category/deletecategory/{base64(cat_id)}` | — | Redirect + flash. |

**Validation:** Duplicate `(brand_name, category_name)` → error.

### 6.5 Products

| Action | Method | Endpoint | Body | Response |
|--------|--------|----------|------|----------|
| List | GET | `/ProductDetails` | — | HTML. |
| Add | POST | `/addproductdetails` | See below | Redirect + flash. |
| Edit form | GET | `/editProductDetails/{base64(pro_id)}` | — | HTML. |
| Update | POST | `/Product/updateProductdetails` | See below | Redirect + flash. |
| Delete | GET | `/Product/deleteproduct/{base64(pro_id)}` | — | Redirect + flash. |

**Add product (POST `/addproductdetails`):**

- `brand_name` (brand id), `category_name` (category id), `product_name`, `product_desc`, `product_tech`
- `product_img`: file (multipart). **Required** on add.

**Update product (POST `/Product/updateProductdetails`):**

- Same as add, plus:
  - `prodId` (product id)
  - `old_image` (current image filename). If `product_img` is empty, `old_image` is kept.

**Validation:** Duplicate `(brand_name, category_name, product_name)` → error.

---

## 7. Database Reference

- **DB:** MySQL, name `vedantlights_db`. Config: `app/Config/Database.php`.

### 7.1 Tables

**`brand_details`**

| Column | Type | Notes |
|--------|------|--------|
| `brand_id` | PK, auto-increment | |
| `brand_name` | string | Unique per app logic |
| `is_delete` | 0/1 | 0 = active |

**`category_details`**

| Column | Type | Notes |
|--------|------|--------|
| `cat_id` | PK, auto-increment | |
| `brandId` | FK → `brand_details.brand_id` | |
| `caterogyName` | string | **Typo:** “caterogy” not “category” |

**`product_details`**

| Column | Type | Notes |
|--------|------|--------|
| `pro_id` | PK, auto-increment | |
| `brand_id` | FK → `brand_details.brand_id` | |
| `catId` | FK → `category_details.cat_id` | |
| `pro_name` | string | |
| `pro_desc` | text | |
| `pro_tech` | text | |
| `pro_img` | string | Image filename only |

**`user_details`** (admin login)

| Column | Type | Notes |
|--------|------|--------|
| `user_id` | PK | Stored in session as `mst_id` |
| `username` | string | Login form: `user_name` |
| `password` | string | Plain text in `ModelLogin::checkLogin` |

### 7.2 Relationships

- **Brand → Categories:** one-to-many (`brandId`).
- **Brand → Products:** one-to-many (`brand_id`).
- **Category → Products:** one-to-many (`catId`).

---

## 8. File Uploads

- **Product images:** `multipart/form-data`, field `product_img`.
- **Storage:** `uploads/Product/` (project root). Filename stored as-is in `product_details.pro_img`.
- **URL:** `{baseURL}uploads/Product/{filename}` (e.g. `https://www.vedantlights.com/uploads/Product/xyz.png`).

**React:** Use `FormData`, append `product_img` and other fields; POST to add/update product endpoints. For update, when not changing image, omit file and send `old_image`.

---

## 9. Authentication & Sessions

- **Driver:** File-based sessions. Config: `app/Config/Session.php`.
- **Cookie:** `ci_session`. Expiration: 7200 seconds.
- **Session keys used:** `mst_id`, `username`, `admin_loggedin`.
- **Protected actions:** All admin (Brand, Category, Product, Login dashboard). Unauthenticated → login view.

**React (same-origin):** Use `fetch(..., { credentials: 'include' })` so the session cookie is sent. No bearer token unless you add it.

**React (different origin):** Configure CORS in CodeIgniter (or server) and ensure cookies are allowed (e.g. `SameSite=None; Secure` if cross-origin). Session config may need adjustments.

---

## 10. CSRF & Security

- **CSRF:** **Disabled** in `app/Config/Filters.php` (commented out). No CSRF token required for API calls currently.
- If you enable CSRF later: token name `csrf_test_name`, header `X-CSRF-TOKEN`. Cookie `csrf_cookie_name` (see `app/Config/Security.php`).

---

## 11. IDs in URLs (Edit / Delete)

- **Edit / delete** use **base64-encoded** IDs in the path:
  - Edit brand: `editBrand/{base64(brand_id)}`
  - Edit category: `editcategory/{base64(cat_id)}`
  - Edit product: `editProductDetails/{base64(pro_id)}`
  - Delete product: `Product/deleteproduct/{base64(pro_id)}`
  - Delete category: `Category/deletecategory/{base64(cat_id)}`

Models decode with `base64_decode()` before DB use. **React:** Send same base64 ids when calling these routes.

---

## 12. Static Assets (for reference)

- **Web assets:** `web_assets/` (CSS, JS, images).  
  - Logo: `web_assets/images/logo/logo.png`.  
  - Favicon: `web_assets/images/fav.png`.
- **Admin assets:** `admin_asset/`.
- **Product images:** `uploads/Product/`.

Base URL for assets: same as API base (e.g. `https://www.vedantlights.com/`).

---

## 13. Summary: Minimal Backend Work for React

1. **Add JSON API routes** (new controller or methods) that:
   - Return brands, categories, products, single product (public).
   - Return dashboard stats, brand/category/product lists for admin.
   - Reuse existing **Models** (`HomeModel`, `BrandModel`, `CategoryModel`, `ProductModel`, `ModelLogin`).
2. **Keep** `POST /sendmail`, `POST /Login/check_login`, `POST /Product/CategoryDetails` behavior; ensure they **return JSON** when called from React (they already do for AJAX, but verify).
3. **Add** JSON responses for add/update/delete (brand, category, product) **or** keep redirects and use them from React with `redirect` handling (less ideal).
4. **CORS:** If React is on another origin, add CORS headers for your API routes.
5. **Optional:** Add a small middleware/filter to return 401 JSON for unauthenticated admin API calls instead of HTML login page.

Use this doc as the single reference for URLs, request/response shapes, DB fields, and auth when building the React frontend.
