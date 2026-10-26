# TaxiRide - Homa Bay taxi operations system

PHP 8 + MySQL web system for taxi operations in Homa Bay, Kenya. Three users: **Passenger (customer)**, **Driver** and **Admin**.

## Install on XAMPP (Windows)
1. Install XAMPP and start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Copy the `taxiride` folder into `C:\xampp\htdocs\`.
3. Open http://localhost/phpmyadmin, click **Import**, choose `database.sql` and click **Import**. This creates the `taxiride` database with sample data.
4. Open http://localhost/taxiride/ in your browser.
5. If your MySQL has a password, or the folder has another name, edit `config.php` (`DB_PASS`, `BASE_URL`).

## Demo accounts
| Role | Email | Password |
|---|---|---|
| Admin | admin@taxiride.co.ke | admin123 |
| Driver | peter@taxiride.co.ke | driver123 |
| Passenger | achieng@example.com | customer123 |

Other drivers: james, moses, tom, collins, victor (@taxiride.co.ke, driver123). Other passengers: brian, faith, kevin, mercy, dennis, joyce, samuel (@example.com, customer123).

## What is built
**Public:** landing page with live fare calculator, coverage list, register, login, forgot/reset password.

**Passenger:** dashboard, book a ride (point to point or hourly hire, live fare meter, promo codes, saved places), my bookings with search and filters, booking detail with status timeline, cancel, rate driver, printable receipt, saved places, support tickets, notifications, profile.

**Driver:** dashboard with online/offline switch, accept or decline assigned trips, step-by-step trip status (on the way, start, complete), record cash or M-Pesa payment, trip list, earnings with commission and CSV export, ratings, vehicle and document expiry view, support tickets, notifications, profile.

**Admin:** dashboard with KPIs and charts, dispatch board, all bookings (search, filters, CSV), new booking for phone/WhatsApp/stage, booking detail (assign driver and vehicle, remove driver, cancel, complete, record payment), drivers (add, edit, suspend), vehicles (add, edit, remove, document dates), customers (suspend), places, routes and fares, promo codes, notifications, profile, support ticket replies.

**Rules enforced:** expired licence, insurance or inspection blocks assignment; drivers cannot go online with expired documents; vehicle seat limits; promo validation; M-Pesa code format; role-based access on every page; CSRF tokens on all forms; prepared statements; hashed passwords.

## Planned next
Admin payments verification, reports, ratings overview, support ticket list, announcements, audit log and settings pages. The sidebar shows these links automatically once the files exist.

## Notes
- Fares in `database.sql` are sample values for Homa Bay routes. Change them in Admin > Routes and fares.
- Sample data dates are relative to the moment you import the database.
- Password reset shows the reset link on screen because the project has no email server.
