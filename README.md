#  TAXI GO API

A complete Ride-Sharing and Taxi management REST API built with **Laravel** (Laravel Sanctum for authentication) and **PostgreSQL**.

---

##  Core Features

* **Secure Authentication:** Register, login, and password reset workflows via Laravel Sanctum.
* **Passenger Lifecycle:** Search for nearby drivers, create ride requests (standard or shared), and submit ratings.
* **Driver Workflows:** Join as a driver, upload vehicle/insurance documents, toggle availability, and accept rides.
* **High-Frequency GPS Tracking:** Real-time location tracking (latitude, longitude, speed, and heading).
* **Admin Management:** Driver approval and document verification simulation.

---

##  Tech Stack

* **Backend Framework:** Laravel 11+
* **Database:** PostgreSQL
* **Authentication:** Laravel Sanctum (Bearer Tokens)

---

##  Quick Setup

```bash
# 1. Clone the repository
git clone [https://github.com/lakroune/taxi-go-api.git](https://github.com/lakroune/taxi-go-api.git)
cd taxi-go

# 2. Install dependencies
composer install
npm install

# 3. Setup environment files
cp .env.example .env

# 4. Generate app key & run migrations
php artisan key:generate
php artisan migrate --seed

# 5. Start the local server
php artisan serve

```
---
##  API Documentation
Comprehensive API documentation is available in the `docs/` directory, detailing all endpoints, request/response formats, and authentication requirements.
[https://lakroune.io/](https://lakroune./taxi-go.git)
---