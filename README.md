# Smart Energy Consumption Tracker

A PHP-based web application to track and manage home energy consumption. This tool allows users to record appliance usage, calculate energy costs based on TANGEDCO (Tamil Nadu) style categories, and export reports to PDF.

## 🚀 Step-by-Step Setup Guide

Follow these steps to get the application running on your local machine (using XAMPP).

### 1. Prerequisites
- Install [XAMPP](https://www.apachefriends.org/index.html) or any WAMP/LAMP stack.
- Basic knowledge of PHP and MySQL.

### 2. Project Placement
1.  Open your XAMPP installation folder (usually `C:\xampp`).
2.  Navigate to the `htdocs` directory.
3.  Place the `smartenergy` folder inside `htdocs`.
    - Path: `C:\xampp\htdocs\smartenergy`

### 3. Database Configuration
1.  Open your browser and go to `http://localhost/phpmyadmin`.
2.  Create a new database named **`smart_energy`**.
3.  Select the `smart_energy` database.
4.  Click on the **Import** tab.
5.  Choose the `energy_consumption.sql` file from your project folder and click **Import**.
    - *Note: Ensure the `users` and `appliances` tables are also created if they aren't fully defined in the .sql file provided. You may need to create a user account via `signup.php` first.*

### 4. Database Connection Settings
If your MySQL root password is not empty, update the configuration:
1.  Open `includes/db.php`.
2.  Modify the `$username` and `$password` variables according to your local setup.

### 5. Running Migrations & Seeding (Crucial)
To ensure the database has the latest structure and appliance data:
1.  Run the migration script: `http://localhost/smartenergy/migrate_db.php`
    - This adds necessary columns like `start_date` and `end_date`.
2.  Seed the appliance data: `http://localhost/smartenergy/seed_tn_appliances.php`
    - This populates the database with typical Tamil Nadu household appliances (Wet Grinder, ACs, etc.).

### 6. Accessing the App
1.  Open your browser and go to `http://localhost/smartenergy/`.
2.  You will be redirected to `login.php`.
3.  If you don't have an account, click "Sign Up" or go to `signup.php`.

---

## 🛠️ Key Features
- **User Authentication**: Secure Login and Signup system.
- **Appliance Tracking**: Add and manage daily/weekly usage of various household appliances.
- **Consumption Calculation**: Automatic KWh and cost calculation.
- **PDF Reports**: Export your energy consumption summary to a PDF file for record-keeping.
- **TN Specialized**: Includes pre-set wattage for common appliances used in Tamil Nadu households.

## 📂 Project Structure
- `index.php`: Main entry point (redirects to login).
- `dashboard.php`: The main user interface for tracking energy.
- `export_pdf.php`: Handles the PDF generation logic.
- `includes/`: Contains core database connection and helper functions.
- `css/`: Styling for the application.

## 📝 Tips for Good Results
- **Consistent Data**: Input your hours used daily for accurate monthly projections.
- **Update Migrations**: Always run `migrate_db.php` if you pull fresh code to ensure your table schema matches.
- **Seed Data**: Run `seed_tn_appliances.php` once at the start to have a wide range of appliances to choose from.
