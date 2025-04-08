# 🤌 Pinced – Dish Discovery Scraper

**Pinced** is a web app that helps users discover which restaurants serve a specific dish in their area.  
It targets small/local restaurants with publicly available, text-based menus – a niche often left out by big food platforms.

This project blends basic web scraping with a minimal interface and database-powered features to create a useful tool for food lovers.

---

## Idea

Most food delivery and discovery apps don’t let you search for a **specific dish** – especially across **small local spots** with old-school menus.
Pinced fills that gap by scraping available menus and letting users input the name of a dish to find matches nearby.

---

## Features

### ✅ MVP Goals
- Dish search by keyword (e.g. “carbonara”, “pad thai”)
- Basic scraping from selected restaurants with HTML/text-based menus
- Clean Blade-based UI for focused interaction
- Display of results with menu snippets and links to source

### 🧭 In Progress / Planned
- User accounts (Laravel auth)
- Saved searches (dish + location)
- Favorite restaurants or dishes
- Search history
- Smart keyword highlighting in results
- Pagination / filtering by location or cuisine
- Admin panel for data overview and scraping health

---

## Tech Stack

- **Backend**: Laravel
- **Frontend**: Blade (Laravel templating)
- **Scraping**: Laravel HTTP Client + Symfony DomCrawler
- **Database**: MySQL (users, searches, favorites, etc.)
- **Hosting (planned)**: Vercel for frontend, Render or similar for backend/API

---

## Architecture Overview

- App scrapes simple restaurant menus for dish keywords
- Scraped dishes are cached in the DB to improve UX
- Core table: `dishes` with fields: name, snippet, source link, etc.

Planned Features:
- Laravel Auth
- Saved searches
- Favorites