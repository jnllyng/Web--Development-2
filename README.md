# Web Development 2 Coursework

Portfolio of weekly exercises, assignments, and a final project built with PHP, JavaScript, and MySQL.

## Quick Start
- Place the folder under your web root (e.g., `C:\\xampp\\htdocs\\Web-Development-2`).
- Start Apache/MySQL (XAMPP) and open `http://localhost/Web-Development-2/` in the browser.
- Point database‑backed examples to your local MySQL credentials (see `Project/includes/db_connect.php` and Module 3 CRUD demos).

## Folder Map
- `Module 1` — PHP basics: syntax drills, embeds, hashes, and first lightbox assignment.
- `Module 2` — Forms and validation: client/server validation, thank-you flows, and Assignment 2 group work.
- `Module 3` — CRUD with MySQL: insert/select/update/delete practice, blog assignments, and seeds (`posts.sql`).
- `Module 4` — JavaScript interactions: DOM scripting, AJAX-style exercises, and Assignment 4 variants.
- `Module 5` — Reference notes (`JavaScript_Topics.html`).
- `PHP beginner` — Simple starters (`flipCoin.php`, `textAnalyzer.php`).
- `6 Cookies & Sessions` — Cookie/session handling demos.
- `7 File Upload Demo Files` — Basic and filtered upload examples.
- `C6 Starting Files` — Higher/lower game with JSON highscores and people data.
- `C7 Starting Files` — Image upload/resizing (GD check, uploads, php-image-resize library).
- `Project` — Capstone CRUD app (animals/plants/species gallery) with admin area, auth, comments, and uploads.
- `webd-2013_prep*.zip` — Archived prep materials.

## Notable Entry Points
- Module highlights: `Module 1/webd-2013_assignment1_group_8/index.php`, `Module 2/A2 Starting Files/p2form.html`, `Module 3/Assignment_3_WEBD-2013/index.php`.
- Upload demos: `7 File Upload Demo Files/fileupload/upload_no_filter.php` and `upload_and_filter.php`.
- Games: `C6 Starting Files/HigherLower.php`.
- Image resizing: `C7 Starting Files/fileUpload.php` (saves originals/medium/thumbnail to `uploads/`).
- Capstone: `Project/index.php` (public gallery) and `Project/admin/dashboard.php` (admin CRUD).

## Running the Capstone Project
1) Import schema/data: use `posts.sql` for Module 3 demos and the provided `Project` tables (create via `Project/includes/db_connect.php` connection details).  
2) Configure DB credentials in `Project/includes/db_connect.php`.  
3) Ensure `Project/uploads/` and `C7 Starting Files/uploads/` are writable for image handling.  
4) Access public pages at `/Project/index.php`; admin at `/Project/admin/dashboard.php` (set initial user in DB).

## Testing File Upload/Resize (C7)
- Hit `/C7 Starting Files/fileUpload.php`.
- Upload a JPEG; originals plus `_medium` and `_thumbnail` variants are written to `C7 Starting Files/uploads/`.
- Use `check_gd.php` if you need to verify GD support.

## Notes
- Many folders contain `__MACOSX` artifacts from zips; safe to ignore.
- Some example files are intentionally empty placeholders for in-class exercises.
