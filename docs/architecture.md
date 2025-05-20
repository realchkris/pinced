## Folder Structure

- `Scraper/` – Fetches and coordinates scraping logic
- `Detector/` – Pulls structured data (JSON-LD, Microdata), Locates content inside HTML
- `Helper/` – Pure functions for content validation, noise filtering
- `DTO/` – Typed data objects (e.g. RestaurantDTO)
- `Deduplicator/` – Logic to compare and avoid duplicate restaurants
- `Orchestrator/` – Top-level controller for research flow
