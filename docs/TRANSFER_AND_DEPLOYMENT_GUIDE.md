# TRANSFER AND DEPLOYMENT GUIDE
## IT 107 - Information Assurance and Security 1 Final Laboratory Project
**Project Name:** Secure Farmers Registration, Assistance and Monitoring System  
**Student:** Jerime Tagalog (`jerime.tagalog@csucc.edu.ph`) · Student ID: 2020-1050  
**Prepared For:** Machine-to-machine transfer, demo presentation, and lab grading.

---

### Step 1: Requirements on Target Laptop
Ensure the new laptop has:
1. **XAMPP for Windows** installed (default path: `C:\xampp`) with:
   - Apache (PHP 8.0+)
   - MySQL / MariaDB
2. A modern web browser (Google Chrome, Microsoft Edge, Firefox, or Brave).

---

### Step 2: Transfer the Project Files
1. Copy the entire `farmers_system` folder into the target laptop's XAMPP `htdocs` directory:
   ```text
   C:\xampp\htdocs\farmers_system
   ```
2. Confirm the directory structure looks like this:
   ```text
   C:\xampp\htdocs\farmers_system\
   ├── css\
   ├── database\
   │   ├── full_database_dump.sql   <-- complete schema + preloaded demo accounts
   │   ├── schema.sql
   │   └── seed_data.sql
   ├── docs\
   ├── img\
   │   └── farmer-ricefield.jpg    <-- real background photo (Guilin karst landscape)
   ├── js\
   ├── logs\
   ├── php\
   ├── scripts\
   ├── tests\
   ├── vendor\                      <-- included (PHPMailer & autoload dependencies)
   ├── config.php                   <-- included local config
   ├── setup.bat                    <-- one-click automated setup
   ├── setup.ps1
   └── index.php
   ```

---

### Step 3: Start XAMPP Services
1. Open **XAMPP Control Panel**.
2. Click **Start** for **Apache**.
3. Click **Start** for **MySQL**.

---

### Step 4: Run the One-Click Setup
In Windows File Explorer, navigate to:
```text
C:\xampp\htdocs\farmers_system
```
Double-click:
👉 `setup.bat`

What this script automatically does:
- Verifies MySQL is reachable.
- Creates the `farmers_system_db` database in MySQL.
- Imports `database/full_database_dump.sql` containing all tables and pre-seeded accounts.
- Ensures `config.php` and runtime `logs/` directory are prepared.

*(Alternatively, you can import `database/full_database_dump.sql` directly inside http://localhost/phpmyadmin).*

---

### Step 5: Test and Access the System
Open your web browser and navigate to:
👉 **`http://localhost/farmers_system`**

#### Pre-Configured Demo Accounts:
| Role | Username | Password | Purpose |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin_monitor` | `Admin@2026!` | Access Security Monitoring Dashboard (`admin_dashboard.php`) |
| **Farmer 1** | `farmer_juan` | `Farmer@2026!` | Submit and view farm assistance requests (`farm_requests.php`) |
| **Farmer 2** | `farmer_maria` | `Farmer@2026!` | Test lockouts, password history, and OTP verification |

---

### Step 6: Verifying System Integrity (Self-Test)
Open Command Prompt or PowerShell and run:
```bash
C:\xampp\php\php.exe tests/self_test.php
```
You should see:
```text
Self-Test Summary: 8 Passed, 0 Failed
```
This confirms that AES-256 database encryption, password hashing, SQL injection immunity, XSS sanitization, progressive lockout tiers (15s / 30s / 60s), and immutable logging are all working flawlessly on the new machine.

---

### Step 7: Verifying Email (SMTP Test)
To confirm the SMTP out-of-band OTP delivery is working, run:
```bash
C:\xampp\php\php.exe test_mail.php
```
You should see:
```text
SUCCESS: OTP code sent to jerime.tagalog@csucc.edu.ph
```
Check the inbox for the OTP test email. If it fails, verify that `config.php` contains the correct Gmail App Password for `jerime.tagalog@csucc.edu.ph`.
