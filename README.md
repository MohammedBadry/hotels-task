# MultiSupplier Hotel Search API

## 📦 Setup
```bash
git clone https://github.com/MohammedBadry/hotels-task.git
composer install
cp .env.example .env

Make sure that the session driver is file (We are not using database recently so there is no need for migrations)
SESSION_DRIVER=file

php artisan key:generate
php artisan serve



## 🔌 API Integrations Setup

This project integrates with the following third-party hotel provider APIs:

- **Amadeus**  
- **Hotelbeds**  
- **Booking.com** (via RapidAPI)  
- **Tripadvisor** (via RapidAPI)

To enable these integrations, you must register on each platform to obtain credentials, then set them in your `.env` file as shown below.

---

### ✅ Environment Variables

Add these variables to your `.env` file:

```env
# Amadeus
AMADIUS_API_KEY=tn8CJXpdB82Nf0bLt7MtHy556n9EQLrC
AMADIUS_API_SECRET=anLFLSOeViS8aaAS

# Hotelbeds
HOTELBEDS_API_KEY=80ceb5560ac5a93d2de657fcd14ccec5
HOTELBEDS_API_SECRET=6ea4ca2ce1

# Booking.com via RapidAPI
BOOKING_API_KEY=ffc2dd5ea2msh7fb89924295849dp103a6djsn54b255d62ac8

# Tripadvisor via RapidAPI
TRIPADVISOR_API_KEY=ac9de775e1mshf8a11ee814cd0f7p1841dejsn057b8b254980
```

---

### 🧭 How to Get API Credentials

#### Amadeus

1. Visit: [https://developers.amadeus.com](https://developers.amadeus.com)
2. Register a developer account.
3. Create a new **Self-Service Application**.
4. Copy your **API Key** and **API Secret**.
5. Add them to `.env` as `AMADIUS_API_KEY` and `AMADIUS_API_SECRET`.

---

#### Hotelbeds

1. Visit: [https://developer.hotelbeds.com](https://developer.hotelbeds.com)
2. Sign up for an account and wait for approval (may take 24–48 hours).
3. Once approved, create a project and choose the **Hotel Content API** and/or **Booking API**.
4. Get your **API Key** and **Secret** from your dashboard.
5. Add them to `.env` as `HOTELBEDS_API_KEY` and `HOTELBEDS_API_SECRET`.

---

#### Booking.com via RapidAPI

1. Visit:
[https://rapidapi.com/DataCrawler/api/booking-com15/playground/apiendpoint_8f30418d-4a60-45bf-a500-c992fd4bda43](https://rapidapi.com/DataCrawler/api/booking-com15/playground/apiendpoint_8f30418d-4a60-45bf-a500-c992fd4bda43))
3. Sign in or create a RapidAPI account.
4. Subscribe to the **Free Plan**.
5. Copy the **X-RapidAPI-Key** from the endpoints tab.
6. Add it to `.env` as `BOOKING_API_KEY`.

---

#### Tripadvisor via RapidAPI

1. Visit: [https://rapidapi.com/DataCrawler/api/tripadvisor16/playground/apiendpoint_6c187918-f989-4c63-bf1e-b2e38efe3152](https://rapidapi.com/DataCrawler/api/tripadvisor16/playground/apiendpoint_6c187918-f989-4c63-bf1e-b2e38efe3152)
2. Sign in or create a RapidAPI account.
3. Subscribe to the **Free Plan**.
4. Copy the **X-RapidAPI-Key** from the endpoints tab.
5. Add it to `.env` as `TRIPADVISOR_API_KEY`.

---
I added a postman collection file in the main directory you can use it "hotels-task.postman_collection.json"
---

Once you have added the required credentials to your environment file, the APIs will be available for use in the application.
