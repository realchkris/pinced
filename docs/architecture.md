# 📐 Architecture – Pinced

Pinced is a Laravel-based web app that lets users discover which restaurants serve a specific dish by scraping publicly available restaurant menu pages.

This document outlines the system architecture and data flow.

---

## 🧱 Core Components

### 1. **Search Form (Frontend)**
- Blade form (`/search`)
- User inputs a dish name (e.g. “carbonara”)
- Optional: location field (future)

---

### 2. **Search Controller (App\Http\Controllers\SearchController)**
- Accepts the form input
- Validates user input
- Checks if results for that dish already exist in the DB
  - If found → returns cached results
  - If not found → triggers scraping logic

---

### 3. **Scraper Service (App\Services\DishScraperService)**
- Contains logic to scrape hardcoded restaurant URLs
- Parses HTML using Symfony DomCrawler
- Extracts dish matches + context (menu snippet)
- Returns a collection of matches

---

### 4. **Database**
**`dishes` Table Schema:**

| Column         | Type     | Purpose                              |
|----------------|----------|--------------------------------------|
| id             | integer  | Primary key                          |
| name           | string   | Dish name (search term match)        |
| menu_snippet   | text     | Portion of the menu where it was found |
| restaurant_url | string   | Base domain (e.g. trattoriapippo.it) |
| source_link    | string   | Full URL of the menu page            |
| scraped_at     | datetime | When it was scraped                  |
| created_at     | datetime | Laravel auto timestamp               |
| updated_at     | datetime | Laravel auto timestamp               |

---

## 🔄 Data Flow (MVP)

```
User → Search Form
     → SearchController
         ├─> Check DB for cached results
         └─> DishScraperService (if not found)
               └─> Scrape restaurant sites
                     └─> Match dish name
                     └─> Save results to DB
     → Results Page (Blade)
```

---

🔐 Authentication (Planned)

- Laravel Breeze or Jetstream
- Will allow:
    - Saved searches
    - Search history
    - Favorites

---

🧭 Future Features
- Admin panel to manage scraping jobs
- API to expose search functionality
- Background jobs to schedule scraping
-  Dynamic location-aware search