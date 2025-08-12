# PDFSimba

**PDFSimba** is a powerful, web-based document tools platform built with **Flask**, **PHP**, and **JavaScript**, offering features like PDF conversions, editing, signing, and dashboard-based file management.

The system runs on two Flask backends:
- **Main Backend (Port 5000)** — handles conversions and features for the public-facing site.
- **Dashboard Backend (Port 5001)** — handles conversions and tools inside the authenticated user dashboard.

The platform uses the **`pdfsimba_db`** database for storing user accounts, dashboard data, and related records.  
**Important:** Import the provided `pdfsimba_db.sql` file to set up the database before running the application.

---

## Project Structure

### Root Directory
| File/Folder | Description |
|-------------|-------------|
| `index.php` / `index.html` | Landing page for PDFSimba (main public interface). Connects to `Backend/app.py` for conversions and tools. |
| `tools.php` | Similar to the index page but dedicated to listing and accessing all available PDF and document tools. |
| `login.php` / `signup.php` / `terms.php` | Authentication pages and terms of service for the platform. |
| `CSS/` | Contains stylesheets for public-facing pages. |
| `JS/` | Contains JavaScript files used by the main site (e.g., `pdf-tools.js`) for connecting frontend UI to Flask backend APIs. |
| `media/` | Static image assets (e.g., logos, icons, UI images) for the public site. |
| `uploads/` | Temporary storage for user-uploaded files used in conversions on the main site. |
| `converted/` | Stores output files generated from conversions on the main site. |
| `venv/` | Python virtual environment containing all dependencies for Flask and backend operations. |
| `Backend/` | Main Flask backend for the public site (Port 5000). Handles file processing, conversions, and API routes. |

---

### Backend (Main Site Backend)
| File/Folder | Description |
|-------------|-------------|
| `app.py` | Main Flask entry point (Port 5000). Serves API routes for file conversions, editing, and signing. |
| `uploads/` | Backend's temporary upload storage for processing files. |
| `converted/` | Backend's output folder for processed files. |
| Other `.py` files | Conversion scripts (e.g., PDF → Word, Word → PDF, PDF merge, PDF split, PDF sign, etc.). |

---

### Dashboard (User Dashboard)
| File/Folder | Description |
|-------------|-------------|
| `index.php` | Dashboard homepage after user login. |
| `profile.php` | User profile page in the dashboard. |
| `conversion-page-*.php` | Specific pages for individual dashboard tools (e.g., dashboard PDF to Word, Word to PDF). |
| `JS/` | JavaScript files for dashboard conversions and tool UI. Connects to the dashboard backend (Port 5001). |
| `uploads/` | Temporary storage for user uploads inside the dashboard. |
| `converted/` | Stores dashboard-generated converted files. |
| `Backend/` | Dashboard's dedicated Flask backend (Port 5001) with similar functionality to the main backend but scoped to dashboard tools. |
| `DashboardBackend/` | PHP files handling dashboard-specific logic (e.g., authentication, database operations). |

---

### Dashboard/Backend (Dashboard Flask Backend)
| File/Folder | Description |
|-------------|-------------|
| `app.py` | Dashboard-specific Flask entry point (Port 5001). Handles dashboard API calls for file processing. |
| `uploads/` | Temporary uploads folder for dashboard backend processing. |
| `converted/` | Output folder for dashboard backend conversions. |
| Other `.py` files | Dashboard tool-specific conversion scripts. |

---

## Conversions Available in PDFSimba

**Document to PDF**
- Word to PDF  
- Excel to PDF  
- PowerPoint to PDF  
- JPG to PDF  
- eBooks to PDF  
- iWork to PDF  

**PDF to Other Formats**
- PDF to Word  
- PDF to Excel  
- PDF to PowerPoint  
- PDF to JPG  
- PDF to PNG  
- PDF to PDF/A  

**PDF Tools**
- Merge PDF  
- Split PDF  
- Protect PDF  
- Unlock PDF  
- Sign Document  
- Edit PDF  

---

## How It Works

1. **Frontend Interaction**
   - Users interact via the main site (`index.php`, `tools.php`) or the dashboard (`dashboard/index.php`).
   - JavaScript files (`pdf-tools.js` in `JS/`) send requests to the appropriate backend using `fetch()`.

2. **Backend Processing**
   - The Main Backend (Port 5000) processes public tool requests.
   - The Dashboard Backend (Port 5001) processes logged-in user dashboard requests.
   - Each backend saves uploads to `uploads/` and final results to `converted/`.

3. **File Conversion Flow**
   - User uploads a file via the frontend.
   - Backend script processes it (conversion, compression, merging, signing, etc.).
   - Processed file is stored in `converted/` and served back to the frontend for download.

---

## Running PDFSimba Locally

### Requirements
- Python 3.9+
- Flask
- PHP (for frontend and dashboard logic)
- Virtualenv
- Required Python packages in `requirements.txt`
- MySQL/MariaDB database named `pdfsimba_db`
- Import `pdfsimba_db.sql` before running

### Steps
```bash
# Activate virtual environment
source venv/bin/activate   # Mac/Linux
venv\Scripts\activate      # Windows

# Run Main Backend
cd Backend
python app.py

# Run Dashboard Backend (in a new terminal)
cd Dashboard/Backend
python app.py
