# MediShop

## Project Scenario Summary

**MediShop** is a web-based **Online Medicine Shop** that lets users browse medicines by category, vendor, and name, then place orders through a simple e-commerce flow. The application is built for a **Web Technologies** course assignment and demonstrates authentication, admin management, shopping cart behavior, checkout, and order tracking with both traditional form posts and AJAX updates.

The system supports **two registered roles** and a **guest (non-logged-in)** experience:

| Role | Description |
|------|-------------|
| **Admin** | Manages the store: categories (liquid/solid), medicines (CRUD with images), customer list, pending orders (accept/reject via AJAX), and order history. |
| **Customer** | Registers and logs in to search medicines, view details, add items to cart (AJAX), checkout, pay, track orders, cancel/reorder (AJAX), and maintain profile. |
| **Guest** | Can open the home page and see the medicine catalog encouragement to register or log in; cart, checkout, and order features require a **customer** account. |

**Typical workflow:** Admin adds categories and medicines → customer browses and filters medicines → adds to cart → checkout with shipping address → selects payment method → order is **pending** → admin **accepts** or **rejects** → customer views status and invoice in **My Orders**.

The database ships with **sample categories and medicines** (e.g. Napa 500, Aspirin Protect, Dexo Cough Syrup). **User accounts are not pre-seeded**; create them via the registration page (see [Default User Credentials](#default-user-credentials)).

---

## Technologies & Topics Used

The project combines front-end, back-end, database, and security practices from web technologies coursework.

### Front-End

| Topic | How it is used in this project |
|-------|--------------------------------|
| **HTML5** | Page structure, navigation, forms, tables, medicine cards, admin panels, checkout and invoice views |
| **CSS3** | Layout (Flexbox, CSS Grid), cards, badges, alerts, auth forms, responsive rules (`@media`) |
| **JavaScript** | Client-side validation (`task1_script.js`, `task2_script.js`); AJAX (`XMLHttpRequest`) for cart, medicine search, order status, and order search/filter |

Styles and scripts are grouped by assignment task: `task1_style.css` / `task1_script.js` through `task4_*`.

### Back-End

| Topic | How it is used in this project |
|-------|--------------------------------|
| **PHP** | Server-side logic, sessions, routing via `control/*_process.php` and API endpoints |
| **MVC-style separation** | `control/` (logic), `model/` (database), `view/` (templates) |
| **MySQLi** | Database connection and **prepared statements** (`bind_param`) to reduce SQL injection risk |
| **Sessions & cookies** | Login state, role (`admin` / `customer`), “Remember Me” HMAC cookie |
| **File upload** | Profile pictures and medicine images with MIME type and 2MB size checks |
| **Password security** | `password_hash()` on register/update; `password_verify()` on login |

### Database

| Topic | How it is used in this project |
|-------|--------------------------------|
| **MySQL** | Database name: **`wti`** |
| **Tables** | `users`, `categories`, `medicines`, `cart`, `orders`, `order_items`, `payments` |
| **Keys & integrity** | Foreign keys linking cart, orders, and order items to users and medicines |

### Other Web Topics

| Topic | How it is used in this project |
|-------|--------------------------------|
| **AJAX / JSON** | `cart_*_api.php`, `medicine_search.php`, `orders_search_api.php`, `admin_order_status_api.php`, `customer_order_*_api.php` return JSON for dynamic UI |
| **XSS prevention** | `htmlspecialchars()` when displaying user-generated or database content |
| **Role-based access** | `admin_gate.php`, `customer_gate.php`, and session checks on protected pages/APIs |
| **Responsive UI** | Mobile-friendly navigation and grids in task stylesheets |
| **Apache (XAMPP)** | Local hosting; `index.php` redirects to `view/Home.php` |

> **Note:** This project uses **MySQLi** prepared statements, not PDO. CSRF tokens are not implemented; forms rely on session authentication and server-side validation.

---

## Default User Credentials

`database_task1.sql` creates the schema and **sample medicines**, but **does not insert demo users**. After importing the SQL file, create accounts from **Register** (`view/Registration.php`).

Use these **recommended demo accounts** (same password for easy testing):

| Role | Display Name (example) | Email (example) | Password | How to create |
|------|------------------------|-----------------|----------|----------------|
| **Admin** | Site Admin | `admin@medishop.local` | `Admin@12345` | Register → Account Type: **Admin** |
| **Customer** | Jamie Customer | `user@medishop.local` | `Admin@12345` | Register → Account Type: **Customer** |

**Password rules:** minimum **8 characters** (enforced on register and password change).

**Remember Me:** optional on login; stores a signed cookie for 7 days (`control/auth.php`).

For production or public demos, restrict who can register as **Admin** (registration currently allows choosing Admin from the form).

---

## How to Run the Project

1. Install **XAMPP** and start **Apache** and **MySQL**.
2. Copy this folder to `htdocs`, e.g. `C:\xampp\htdocs\WTProject_08`.
3. In **phpMyAdmin**, import **`database_task1.sql`** (creates database `wti`, tables, and sample categories/medicines).
4. Open in the browser:
   - `http://localhost/WTProject_08/index.php`  
   - or `http://localhost/WTProject_08/view/Home.php`
5. **Register** admin and customer accounts (see table above), then **log in**.
6. If MySQL credentials differ from XAMPP defaults, edit `model/database.php` (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`).

---

## Main Modules (Assignment Tasks)

| Task | Module | Main features |
|------|--------|----------------|
| **Task 1** | Auth & profile | Register, login, remember me, profile view/edit, password change, home catalog |
| **Task 2** | Admin | Dashboard stats, categories, medicines (CRUD + image), customers list, order accept/reject (AJAX), order history |
| **Task 3** | Cart & checkout | Add/update/remove cart (AJAX), cart page, checkout, payment, order success |
| **Task 4** | Customer orders | My orders, live search/filter by status and date (AJAX), order detail, cancel/reorder (AJAX), invoice |

---

## Project Folder Overview

```
WTProject_08/
├── index.php              → Redirects to view/Home.php
├── database_task1.sql     → Schema + sample categories/medicines
├── control/               → Process scripts, APIs, auth, uploads
├── model/                 → database.php (config), mydb.php (queries)
├── view/                  → PHP/HTML pages
├── css/                   → task1–task4 stylesheets
├── js/                    → task1–task4 validation & AJAX
└── uploads/               → profile/ and medicines/ images
```

---

## Security Features (Summary)

- Prepared statements for database queries in `model/mydb.php`
- Hashed passwords (never stored as plain text)
- Escaped output with `htmlspecialchars()` to reduce XSS risk
- Role-based access for admin and customer areas
- Validated file uploads (JPEG/PNG, max 2MB)
- Remember-me cookie signed with HMAC (`hash_hmac` / `hash_equals`)

---

## Quick Links for Reviewers

| Item | Value |
|------|--------|
| App name | MediShop |
| Entry URL | `http://localhost/WTProject_08/` |
| Database | `wti` |
| SQL import file | `database_task1.sql` |
| Guest home | `view/Home.php` |

This README describes the project scenario, technologies used, setup steps, and how to create demo logins for GitHub visitors, instructors, and reviewers.
