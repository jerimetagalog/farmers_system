# SECURE FARMERS REGISTRATION, ASSISTANCE AND MONITORING SYSTEM
## Comprehensive Step-by-Step User & Demonstration Guide

**Course:** IT 107 – Information Assurance and Security 1  
**Institution:** Caraga State University (CSU) – Department of Information Technology  
**Student Name:** Jerime Tagalog  
**Student ID:** 2020-1050  
**Email:** `jerime.tagalog@csucc.edu.ph`  
**Security Settings:** Universal Section 2.3 Settings (MinUser:6, MaxUser:20, MinPwd:8, MaxName:50)  
**GitHub Repository:** [https://github.com/jerimetagalog/farmers_system](https://github.com/jerimetagalog/farmers_system)

---

## Table of Contents
1. [Pre-Flight Checklist & System Startup](#1-pre-flight-checklist--system-startup)
2. [Quick Reference Demo Credentials](#2-quick-reference-demo-credentials)
3. [Module 1: Portal Navigation & Landing Page](#3-module-1-portal-navigation--landing-page)
4. [Module 2: Farmer Registration Flow (5-Step Wizard)](#4-module-2-farmer-registration-flow-5-step-wizard)
5. [Module 3: Registration Email OTP Verification](#5-module-3-registration-email-otp-verification)
6. [Module 4: Farmer Two-Step Authentication (Login)](#6-module-4-farmer-two-step-authentication-login)
7. [Module 5: Farm Assistance Request Management](#7-module-5-farm-assistance-request-management)
8. [Module 6: Idle Session Timeout & Auto-Logout](#8-module-6-idle-session-timeout--auto-logout)
9. [Module 7: Account Recovery & Password History](#9-module-7-account-recovery--password-history)
10. [Module 8: Progressive Account Lockout & Rate Limiting](#10-module-8-progressive-account-lockout--rate-limiting)
11. [Module 9: Administrator Security Monitoring Dashboard](#11-module-9-administrator-security-monitoring-dashboard)
12. [Module 10: Automated CLI Intrusion Detection Script](#12-module-10-automated-cli-intrusion-detection-script)
13. [Module 11: Automated Security Self-Test Suite](#13-module-11-automated-security-self-test-suite)
14. [Module 12: Email (SMTP) Live Test](#14-module-12-email-smtp-live-test)
15. [Module 13: Git Version Control & GitHub Repository](#15-module-13-git-version-control--github-repository)
16. [Troubleshooting & Frequently Asked Questions](#16-troubleshooting--frequently-asked-questions)

---

## 1. Pre-Flight Checklist & System Startup

Before launching the web portal or presenting to your instructor, ensure the local environment is running:

### Step 1.1: Launch XAMPP Services
1. Open the **XAMPP Control Panel** on your Windows workstation.
2. Click **Start** on **Apache** (Default Port: `80` or `8080`).
3. Click **Start** on **MySQL** (Default Port: `3306`).
4. Ensure both services display a green indicator status.

If MySQL fails to start from the Control Panel, use the emergency start script:
```powershell
& "C:\xampp\mysql_start.bat"
```

### Step 1.2: Verify MySQL Database Initialization
Open PowerShell or Command Prompt to confirm the database tables and seed records are active:
```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root -e "USE farmers_system_db; SHOW TABLES;"
```
You should see 8 relational tables:
`activity_logs`, `farm_requests`, `authentication_answers`, `ip_blocks`, `otps`, `password_history`, `security_flags`, and `users`.

### Step 1.3: Access the Portal URL
Open Google Chrome, Microsoft Edge, or Mozilla Firefox and navigate to:
```
http://localhost/farmers_system/
```

### Step 1.4: Project File Structure
```
C:\xampp\htdocs\farmers_system\
├── index.php                  ← Home page (hero background, tabbed showcase)
├── login.php                  ← Split-panel login (left dark + right white form)
├── register.php               ← 5-step registration wizard
├── verify_otp.php             ← OTP verification (registration & login)
├── assistance.php             ← Farmer assistance request portal
├── forgot_password.php        ← Account recovery (security questions)
├── change_password.php        ← Token-protected password reset
├── admin_dashboard.php        ← Admin security monitoring dashboard
├── admin_api.php              ← Admin API (JSON data for dashboard)
├── logout.php                 ← Session destruction & redirect
├── privacy_notice.php         ← RA 10173 privacy notice page
├── api_check_username.php     ← Live username availability AJAX check
├── api_keep_alive.php         ← Session keep-alive AJAX endpoint
├── config.php                 ← SMTP & DB credentials (gitignored)
├── config.sample.php          ← Safe credentials template (committed)
├── test_mail.php              ← SMTP live test (gitignored)
├── css/style.css              ← Global stylesheet (amber/brown theme)
├── css/dashboard.css          ← Admin dashboard styles
├── js/validate.js             ← Client-side age calculation & form UX
├── img/farmer-ricefield.jpg   ← Hero background (Guilin karst landscape)
├── img/hero-farm-bg.svg       ← Fallback SVG background
├── php/security.php           ← AES-256 encrypt/decrypt functions
├── php/auth.php               ← Login, lockout, OTP generation
├── php/validation.php         ← Server-side validation (Section 4.10)
├── php/mailer.php             ← PHPMailer SMTP wrapper
├── php/captcha.php            ← CAPTCHA verification logic
├── php/captcha_image.php      ← GD CAPTCHA image generator
├── php/header.php             ← Global header with banner & nav
├── php/footer.php             ← Global footer with student ID
├── php/db.php                 ← PDO database connection
├── database/schema.sql        ← Database table definitions
├── database/full_database_dump.sql  ← Complete schema + demo accounts
├── scripts/monitor.php        ← CLI intrusion detection (web-blocked)
├── tests/self_test.php        ← 8-assertion automated security test suite
└── docs/                      ← All 9 project documentation files
```

---

## 2. Quick Reference Demo Credentials

The system comes pre-seeded with 3 active, fully verified test accounts. All OTP emails route to `jerime.tagalog@csucc.edu.ph` using Gmail plus-addressing, allowing live verification during demonstration.

| Account Role | Username | Plaintext Password | Registered Email | Contact Number (AES-256 Encrypted) |
| :--- | :--- | :--- | :--- | :--- |
| **Agricultural Administrator** | `admin_monitor` | `Admin@2026!` | `jerime.tagalog@csucc.edu.ph` | `09123456789` |
| **Farmer 1 (Juan)** | `farmer_juan` | `JuanFarmer2026!` | `jerime.tagalog+juan@csucc.edu.ph` | `09876543210` |
| **Farmer 2 (Maria)** | `farmer_maria` | `MariaFarmer2026!` | `jerime.tagalog+maria@csucc.edu.ph` | `09234567891` |

### Pre-Seeded Security Questions & Answers (All Accounts)
| # | Question | Answer (case-insensitive) |
|:-:|:---|:---|
| Q1 | *"What was the name of your first elementary school?"* | `bantay` |
| Q2 | *"What is the name of the street you grew up on?"* | `rizal street` |
| Q3 | *"What was your childhood nickname?"* | `jemboy` |

---

## 3. Module 1: Portal Navigation & Landing Page

### Purpose
Provides a secure landing portal welcoming farmers and agricultural staff, establishes HTTP security headers (`X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`), and enforces the Handout Section 3 layout rules.

### UI Overview (Redesigned):
- **Background:** Real farmer landscape photo (`farmer-ricefield.jpg` — Guilin karst mountains & rice fields) with a ~40% dark gradient overlay for text readability.
- **Hero Text:** Gold-gradient title "*Secure Farmers Registration, Assistance and Monitoring System*" with text-shadow for contrast.
- **Tabbed Showcase:** Three interactive tabs — *Support Programs*, *Security Architecture*, *Crop Advisory*.
- **Right Sidebar:** 2026 Subsidy Allocation progress bars and Municipal Helpdesk contact card.

### Step-by-Step Walkthrough:
1. Open `http://localhost/farmers_system/index.php`.
2. Notice the layout (Handout Section 3 compliance):
   - **System Prospect Banner:** Strictly at the very top — *"Secure Farmers Registration, Assistance and Monitoring System | 2020-1050"*.
   - **Top-Right Navigation:** For guests — only **"Home"**, **"Register"**, and **"Sign In"**. **Logout is never displayed to a guest**.
   - **Hero Actions:** Two primary buttons — **"Register as Farmer"** and **"Sign In"**.
   - **Footer:** *"© 2026 Jerime Tagalog | 2020-1050 | IT 107 IAS 1"*.

---

## 4. Module 2: Farmer Registration Flow (5-Step Wizard)

### Purpose
Allows new farmers to create an account through a **5-step wizard** (no page scrolling), strictly enforcing Section 4 & 5 validation rules, calculating real-time age, generating a CAPTCHA challenge, and capturing RA 10173 consent.

### Wizard Overview:
| Step | Title | Fields |
| :---: | :--- | :--- |
| **1** | Personal Info | ID Number, First/Middle/Family Name, Name Extension, Birthdate, Age |
| **2** | Address | Purok/Street, Barangay, Municipality, Province, Country, ZIP Code |
| **3** | Farm & Contact | Email, Contact Mobile Number, Farm Type, Support Program, Emergency Contact |
| **4** | Credentials | Username, Password, Confirm Password |
| **5** | Security & Consent | 3 Security Questions + Answers, CAPTCHA, Privacy Consent checkbox |

### Step-by-Step Walkthrough:
1. Click **"Register"** in the top navigation or navigate to `register.php`.
2. The page opens on **Step 1 — Personal Info**:
   - **ID Number:** Format `YYYY-NNNN` (*e.g., `2026-9001`*). Duplicate IDs are rejected.
   - **First Name:** Letters and spaces only, Title Case (*e.g., `Gabriel`*). Max 50 characters. Numbers, special characters, and ALL CAPS are blocked.
   - **Middle Name:** Optional. If entered, Title Case is required. Max 50 characters.
   - **Family Name:** Required, Title Case (*e.g., `Mendoza`*). Max 50 characters.
   - **Name Extension:** Optional dropdown (*`Jr.`, `Sr.`, `II`, `III`, `IV`*).
   - **Birthdate:** Select a date at least 18 years in the past.
   - **Age:** Auto-calculated by JavaScript upon selecting birthdate; server validates independently.
   - Click **"Next Step →"** — client-side validates Step 1 before allowing progression.
3. **Step 2 — Address:**
   - **Purok / Street:** Alphanumeric, 3–50 characters.
   - **Barangay, Municipality, Province, Country:** Title Case, letters and spaces only, 2–50 characters each.
   - **ZIP Code:** Exactly 4 numeric digits (*e.g., `8600`*).
   - Click **"Next Step →"**.
4. **Step 3 — Farm & Contact:**
   - **Email Address:** Valid email (*e.g., `jerime.tagalog+demo@csucc.edu.ph`*).
   - **Contact Number (Required):** Exactly 11 digits starting with `09` (*e.g., `09171234567`*). Stored AES-256-CBC encrypted.
   - **Farm Type (Required):** Dropdown — `Rice`, `Corn`, `Vegetable`, `Fruit`, or `Mixed`.
   - **Support Program (Required):** Dropdown — `Fertilizer Subsidy`, `Seed Provision`, `Crop Protection`, `Equipment Loan`, or `Soil Advisory`.
   - **Emergency Contact Number (Optional):** If supplied, must be 11 digits starting with `09`.
   - Click **"Next Step →"**.
5. **Step 4 — Credentials:**
   - **Username:** Starts with a letter, alphanumeric only. Length between **6 and 20** characters (Universal Section 2.3). Live AJAX check verifies uniqueness.
   - **Password:** Minimum **8 characters** (Universal Section 2.3). Live password strength visualizer shows Weak/Medium/Strong.
   - **Confirm Password:** Must match password exactly.
   - Click **"Next Step →"**.
6. **Step 5 — Security & Consent:**
   - **3 Security Questions:** Select 3 different questions from the Section 5 bank (Q1–Q8) and type answers (2–50 chars, letters and spaces only). Stored as one-way Bcrypt hashes.
   - **GD Image CAPTCHA (2-Row Layout):**
     - *Row 1:* Distorted image + "🔄 Refresh Code" button side by side.
     - *Row 2:* Full-width text input field.
   - **Privacy Consent Checkbox:** Must be checked — *"I agree to the collection and processing of my personal information in accordance with RA 10173"*.
   - Click **"Complete Registration"**.
7. **Expected Server-Side Result:**
   - All constraints validated independently of client-side.
   - Password and security answers hashed using Bcrypt.
   - Contact number encrypted using AES-256-CBC with a unique 16-byte random IV.
   - 6-digit OTP generated via `random_int(100000, 999999)`, hashed, dispatched to email.
   - Browser redirects to `verify_otp.php`.

---

## 5. Module 3: Registration Email OTP Verification

### Purpose
Guarantees that an account cannot be logged into until the owner proves ownership of the registered email address via a time-limited out-of-band 6-digit OTP.

### Step-by-Step Walkthrough:
1. The browser lands on `verify_otp.php`.
2. Notice the security indicators:
   - Purpose label: *"Email Verification for Registration"*.
   - Live timer countdown showing the **5-minute expiration window** (Universal Section 2.3).
   - Attempt counter: *"Attempt 1 of 3"*.
3. Open Gmail inbox (`jerime.tagalog@csucc.edu.ph`).
4. Open the email titled: **"Your Verification Code – Farmers System"**.
5. Copy the 6-digit code and enter it on `verify_otp.php`.
6. Click **"Verify Code"**.
7. **Expected Result:**
   - Server verifies the OTP hash against `otps` table.
   - Account is activated: `users.is_verified = 1`.
   - Single-use OTP is marked: `is_used = 1`.
   - Success banner: *"Email verified successfully! You may now sign in."*
   - Browser redirects to `login.php`.

---

## 6. Module 4: Farmer Two-Step Authentication (Login)

### Purpose
Implements multi-factor authentication — username + password + CAPTCHA (Step 1), then a one-time out-of-band email OTP (Step 2) — before any session is established.

### UI Overview (Split-Panel Design):
- **Left Panel (Dark):** Dark brown/amber overlay on farm background. Shows system name, amber icon badges for security features (AES-256, OTP, RA 10173).
- **Right Panel (White):** Clean white card with labels strictly above inputs, embedded password Show/Hide toggle (👁 icon inside input field), and 2-row CAPTCHA.

### Step-by-Step Walkthrough:
1. Navigate to `login.php`.
2. **Step 1 — Primary Credential Check:**
   - **Username:** `farmer_juan`
   - **Password:** `JuanFarmer2026!` (click 👁 to toggle visibility)
   - **CAPTCHA:** View the top-row image → type the code in the full-width input below.
   - Click **"Sign In"**.
3. **Expected Result (Step 1):**
   - Server checks username and verifies password hash via `password_verify()`.
   - **Session is NOT created yet** — defense against premature session creation (Section 8).
   - 6-digit Login OTP generated, hashed, emailed to `jerime.tagalog+juan@csucc.edu.ph`.
   - Browser redirects to `verify_otp.php`.
4. **Step 2 — OTP Second Factor:**
   - Open Gmail, find the **"Your Login Verification Code"** email.
   - Enter the 6-digit code and click **"Verify Code"**.
5. **Expected Result (Step 2):**
   - Server validates OTP hash and 5-minute expiry.
   - `session_regenerate_id(true)` runs to prevent session fixation.
   - Farmer redirected to `assistance.php`.
   - Navigation now shows: **"Hello, Juan!"**, **"My Requests"**, **"Sign Out"**.

---

## 7. Module 5: Farm Assistance Request Management

### Purpose
Provides farmers with self-service agricultural support request submission, real-time AES-256 contact decryption on display, and strict IDOR prevention preventing cross-user data access.

**File:** `assistance.php`

### Step-by-Step Walkthrough:
1. Land on `assistance.php` (redirected after successful login).
2. **Review Farmer Information Card:**
   - Shows full name, student ID, and registered email.
   - **Decrypted Contact Number:** `09876543210` is displayed legibly. In MySQL, `users.contact_number_encrypted` stores an AES-256 base64 ciphertext — the server decrypts it at runtime using the per-record IV.
3. **Review Existing Requests Table:**
   - Columns: Support Program, Farm Type, Date Submitted, Status badge.
   - Status badges: 🟡 *Pending* · 🟢 *Approved* · 🔵 *Dispatched* · 🔴 *Rejected*.
4. **Submit a New Assistance Request:**
   - **Support Program:** Select `'Fertilizer Subsidy'` (or any other program).
   - **Farm Type:** Select `'Rice'`.
   - **Farm Location / Notes:** Type details (*e.g., "Barangay Ampayon, 2 hectare paddy rice farm"*).
   - Click **"Submit Request"**.
5. **Expected Result:**
   - Request saved to `farm_requests` table with `status = 'Pending'`.
   - New row appears immediately in the table with a yellow badge.
   - Audit log event `REQUEST_SUBMITTED` recorded in `activity_logs`.
6. **Anti-IDOR Demonstration (Instructor Defense):**
   - SQL is strictly scoped: `SELECT * FROM farm_requests WHERE farmer_id = :session_user_id`.
   - Even if Juan manipulates browser DevTools or URL parameters, he cannot access Maria's requests.

---

## 8. Module 6: Idle Session Timeout & Auto-Logout

### Purpose
Protects farmer data on unattended terminals by auto-terminating sessions after **15 minutes** of inactivity (Universal Section 2.3), with a 60-second warning at 14 minutes.

### Step-by-Step Walkthrough:
1. Stay on any authenticated page (*e.g., `assistance.php` or `admin_dashboard.php`*).
2. `js/idle_timer.js` monitors mouse movement, keypresses, clicks, and touches.
3. **Warning Phase (At 14 minutes of inactivity):**
   - Accessible modal overlay appears: *"Session Expiring Soon: You have been inactive for 14 minutes. You will be logged out in 60 seconds."*
   - **"Stay Logged In"** sends a keep-alive ping to `api_keep_alive.php` → resets both client timer and PHP session timestamp.
   - **"Log Out Now"** immediately redirects to `logout.php`.
4. **Expiration Phase (At 15 minutes):**
   - Browser redirected to `logout.php?reason=timeout`.
   - PHP session wiped: `session_unset()` + `session_destroy()` + expired cookie.
   - Lands on `login.php?timeout=1` with banner: *"Your session expired due to 15 minutes of inactivity. Please log in again."*

---

## 9. Module 7: Account Recovery & Password History

### Purpose
Enables farmers to recover access via security questions without administrator intervention, while preventing password reuse (Protection S7 — last 3 passwords).

### Step-by-Step Walkthrough:
1. Go to `login.php`.
2. Click the **"Forgot Password?"** link (appears after **2 consecutive failed login attempts**).
3. **Step 1 — Identity Challenge (`forgot_password.php`):**
   - **Username:** `farmer_maria`
   - **CAPTCHA:** Solve the 2-row image CAPTCHA.
   - Click **"Continue"**.
4. **Step 2 — Security Question Verification:**
   - System shows the user's chosen security questions.
   - Select **Q1**: *"What was the name of your first elementary school?"*
   - Type answer: `bantay` *(case-insensitive; server normalizes to lowercase)*.
   - Click **"Verify Answer"**.
5. **Expected Result:**
   - Server normalizes the answer and checks against Bcrypt hash in `authentication_answers`.
   - 6-digit **Password Reset OTP** dispatched to `jerime.tagalog+maria@csucc.edu.ph` with 5-min expiry.
6. **Step 3 — Reset OTP (`verify_otp.php`):**
   - Retrieve 6-digit code from Gmail → enter and submit.
7. **Step 4 — Token-Protected Reset (`change_password.php`):**
   - Server generates 32-byte cryptographic token: `bin2hex(random_bytes(32))`.
   - Redirects to `change_password.php?token=<token>`.
   - **Demonstrate Protection S5:** Copy URL → open in Incognito window → server rejects (no session) → redirects to `login.php`.
8. **Step 5 — Demonstrate Password History (Protection S7):**
   - Try to reuse: `MariaFarmer2026!`
   - Server queries `password_history` (last 3 hashes) → rejects with:  
     *"You cannot reuse any of your last 3 passwords. Please choose a different password."*
9. **Step 6 — Submit New Password:**
   - Enter: `NewMariaSecret!2026` → Confirm → Submit.
   - New Bcrypt hash saved; `password_history` updated; active sessions invalidated.
   - Security Alert Email sent to farmer with IP, timestamp, and browser details.

---

## 10. Module 8: Progressive Account Lockout & Rate Limiting

### Purpose
Thwarts automated brute-force and credential stuffing attacks via 3-tier escalating lockouts and IP-level blocking (Universal Section 2.3).

### Step-by-Step Walkthrough:
1. Open a new **Incognito / Private browser** window → `login.php`.
2. Username: `farmer_juan` | Wrong password: `WrongPass123!` | Solve CAPTCHA → **"Sign In"**.
3. Repeat — observe the progression:
   - **Failure 1:** *"Invalid username or password."* (count = 1)
   - **Failure 2:** *"Invalid username or password."* (count = 2) — **"Forgot Password?"** link appears!
   - **Failure 3 → Tier 1:** *"Account is locked. Please wait **15 seconds**."* + countdown timer shown.
4. **Instructor Talking Points at Tier 1:**
   - Countdown timer appears on screen — **"Sign In"** button is disabled.
   - Check Gmail: **Lockout Security Alert Email** received.
   - Refreshing cannot bypass: `lockout_until` is enforced server-side in MySQL.
5. **Subsequent Tiers:**
   - **Tier 2 (6 failures):** **30 seconds** lockout.
   - **Tier 3 (9 failures):** **60 seconds** lockout.
6. **IP-Level Blocking:**
   - **10 failures** from the same IP within 10 minutes → `ip_blocks` entry inserted.
   - All requests from that IP receive: *"Access temporarily blocked due to repeated security failures. Please contact the agricultural administrator."*

---

## 11. Module 9: Administrator Security Monitoring Dashboard

### Purpose
Provides authorized agricultural administrators a centralized, **read-only** operational dashboard to inspect security events, detect brute-force patterns, review active IP bans, and audit the immutable `activity_logs` table.

### Step-by-Step Walkthrough:
1. Navigate to `login.php`.
2. Admin credentials:
   - **Username:** `admin_monitor`
   - **Password:** `Admin@2026!`
   - **CAPTCHA:** Solve and submit.
3. Enter **Login OTP** dispatched to `jerime.tagalog@csucc.edu.ph` → land on `admin_dashboard.php`.
4. **4 Summary Metric Cards:**
   - 🔴 **Failed Logins (Today):** Total failed auth attempts in last 24 hours.
   - 🟡 **Active Account Lockouts:** Accounts currently in lockout cooldown.
   - 🟠 **Active IP Blocks:** IPs currently banned.
   - 🔵 **Security Flags (Unresolved):** Anomalies from `scripts/monitor.php`.
5. **24-Hour Failed Login Bar Chart:** CSS/SVG chart — hourly failed login counts.
6. **Top 5 Offending IPs Table:** IP addresses with highest failure counts.
7. **Active Security Monitoring Flags:** Patterns flagged by the CLI monitor.
8. **Immutable Audit Logs Table (Protection S11):**
   - 50 most recent events from `activity_logs`.
   - Columns: Timestamp · Event Type · Username · User ID · IP · User Agent · Details.
   - Event filter: `LOGIN_SUCCESS`, `LOGIN_FAILED`, `LOCKOUT_TIER_1`, `REGISTERED`, `PASSWORD_RESET`.
   - **Key point for instructor:** There are **no edit or delete buttons** anywhere on the dashboard. Database privileges (`SELECT` + `INSERT` only on `activity_logs`) prevent any alteration.

---

## 12. Module 10: Automated CLI Intrusion Detection Script

### Purpose
`scripts/monitor.php` runs as a CLI-only background process (web access returns HTTP 403) to detect 4 suspicious attack patterns from the `activity_logs` table.

### The 4 Detection Patterns:
| # | Pattern | Trigger Condition |
|:-:|:---|:---|
| 1 | Rapid Consecutive Failures | ≥ 5 failed logins from one IP/username within 5 minutes |
| 2 | Off-Hours Admin Login | Admin login between 10:00 PM and 6:00 AM |
| 3 | Active IP Blocks | Any active entry in `ip_blocks` |
| 4 | Multi-Account Credential Stuffing | ≥ 3 distinct usernames attempted from one IP within 10 minutes |

### Step-by-Step Walkthrough:
1. Open PowerShell:
   ```powershell
   cd C:\xampp\htdocs\farmers_system
   & "C:\xampp\php\php.exe" scripts/monitor.php
   ```
2. **Expected Terminal Output:**
   ```
   =================================================================
   FARMERS SYSTEM SECURITY MONITORING ENGINE (CLI)
   Execution Time: 2026-11-19 08:00:00 | SAPI: cli
   =================================================================
   [PATTERN 1] Checking for rapid failed logins (>=5 in 5 min)...
               No rapid failed login anomalies detected.
   [PATTERN 2] Checking for off-hours administrator logins (10PM - 6AM)...
               Found: admin_monitor logged in off-hours at 2026-11-19 00:57:22
               Flag recorded into database.
   [PATTERN 3] Checking active IP blocks requiring admin review...
               No active IP blocks detected.
   [PATTERN 4] Checking for multi-account credential stuffing (>=3 users)...
               No distributed credential stuffing detected.
   =================================================================
   Monitoring run complete. Flags recorded: 1 | Duplicate flags ignored: 0
   ```
3. **Demonstrate Duplicate Suppression:** Run script twice. Second run shows `Duplicate flags ignored: 1`.
4. **Demonstrate Browser Block:** Open `http://localhost/farmers_system/scripts/monitor.php` → server returns **HTTP 403 Forbidden** (enforced by `scripts/.htaccess`).

---

## 13. Module 11: Automated Security Self-Test Suite

### Purpose
`tests/self_test.php` runs 8 automated assertions verifying all 13 protections, cryptographic algorithms, error message verbatim compliance, and database immutability.

### Step-by-Step Walkthrough:
1. Open PowerShell:
   ```powershell
   cd C:\xampp\htdocs\farmers_system
   & "C:\xampp\php\php.exe" tests/self_test.php
   ```
2. **8 Test Assertions:**
   | # | Test | Expected |
   |:-:|:---|:---|
   | 1 | Database AES-256 encryption & Bcrypt hashing at rest | PASSED |
   | 2 | SQL injection resistance (`' OR '1'='1`) | PASSED |
   | 3 | XSS escaping via `htmlspecialchars()` | PASSED |
   | 4 | Registration error messages match Section 4.10 verbatim | PASSED |
   | 5 | Password history refusal (last 3 passwords) | PASSED |
   | 6 | Universal lockout tiers (15s / 30s / 60s) | PASSED |
   | 7 | Universal security parameter integrity (MinUser:6, MaxUser:20, MinPwd:8, OTP:5m, Idle:15m, IP:10) | PASSED |
   | 8 | Activity log immutability — zero `UPDATE`/`DELETE` targeting `activity_logs` | PASSED |

3. **Expected Output:**
   ```
   =================================================================
   IT 107 IAS 1 Final Project: System Security Self-Test Suite
   System: Secure Farmers Registration, Assistance and Monitoring System
   Student ID: 2020-1050 | Configuration: Section 2.3 Universal Settings
   =================================================================

   [ PASS ] 1. Database encryption & hashing at rest
   [ PASS ] 2. SQL Injection Resistance (' OR '1'='1)
   [ PASS ] 3. XSS Escaping (htmlspecialchars)
   [ PASS ] 4. Core Registration Error Messages match Handout Section 4.10 verbatim
   [ PASS ] 5. Password history refusal (last 3 passwords)
   [ PASS ] 6. Progressive Lockout Durations (Section 2.3)
   [ PASS ] 7. Universal Security Settings Integrity (Section 2.3)
   [ PASS ] 8. Append-only activity log immutability (Protection S11)

   -----------------------------------------------------------------
   Self-Test Summary: 8 Passed, 0 Failed
   =================================================================
   ```

---

## 14. Module 12: Email (SMTP) Live Test

### Purpose
Verifies that Google SMTP is reachable and OTP emails are dispatched correctly to `jerime.tagalog@csucc.edu.ph` using the configured Gmail App Password.

### Step-by-Step Walkthrough:
1. Run the test script:
   ```powershell
   cd C:\xampp\htdocs\farmers_system
   & "C:\xampp\php\php.exe" test_mail.php
   ```
2. **Expected Terminal Output:**
   ```
   ====================================================
   Testing Live Email OTP Dispatch (Google SMTP)
   Recipient: jerime.tagalog@csucc.edu.ph
   Generated OTP: 483721
   ====================================================

   [ SUCCESS ] Email sent successfully to jerime.tagalog@csucc.edu.ph!
   Check your Gmail inbox (or Spam folder) for the 6-digit OTP code: 483721
   ```
3. Open Gmail — verify the OTP email arrived with the correct 6-digit code.
4. SMTP configuration is in `config.php`:
   - **Host:** `smtp.gmail.com`
   - **Port:** `587` (TLS/STARTTLS)
   - **Username:** `jerime.tagalog@csucc.edu.ph`
   - **Password:** 16-character Google App Password

> **Note:** `test_mail.php` is listed in `.gitignore` and is never committed to GitHub — it is a local testing utility only.

---

## 15. Module 13: Git Version Control & GitHub Repository

### Purpose
Demonstrates secure version control — credentials excluded via `.gitignore`, commit history documented, and project pushed to the student's GitHub repository.

### Repository Information:
| Item | Value |
|:---|:---|
| **GitHub URL** | [https://github.com/jerimetagalog/farmers_system](https://github.com/jerimetagalog/farmers_system) |
| **Clone Command** | `git clone https://github.com/jerimetagalog/farmers_system.git` |
| **Branch** | `main` |
| **Remote** | `origin → https://github.com/jerimetagalog/farmers_system.git` |

### Files Excluded from Git (Protection S11 / `.gitignore`):
| File / Folder | Reason |
|:---|:---|
| `config.php` | Contains live SMTP credentials and DB password |
| `test_mail.php` | Local SMTP test utility — not production code |
| `vendor/` | PHPMailer Composer dependencies — installed locally |
| `logs/` | Live security audit logs — not for version control |
| `*.key`, `*.pem` | Cryptographic key files |

### Step-by-Step Git Demonstration:
1. **Show remote is set:**
   ```powershell
   cd C:\xampp\htdocs\farmers_system
   git remote -v
   ```
   Output:
   ```
   origin  https://github.com/jerimetagalog/farmers_system.git (fetch)
   origin  https://github.com/jerimetagalog/farmers_system.git (push)
   ```
2. **Show commit history:**
   ```powershell
   git log --oneline
   ```
3. **Verify config.php is excluded:**
   ```powershell
   git status
   ```
   `config.php` must NOT appear in the output — proving credentials are never committed.
4. **Verify on GitHub:** Open [https://github.com/jerimetagalog/farmers_system](https://github.com/jerimetagalog/farmers_system) in a browser — confirm all source files are present and `config.php` is absent.

---

## 16. Troubleshooting & Frequently Asked Questions

### Q1: I am not receiving OTP or alert emails.
- **Cause:** Incorrect Google App Password or SMTP port blocked.
- **Solution:**
  1. Verify the 16-character Google App Password in `config.php` for `jerime.tagalog@csucc.edu.ph`.
  2. Ensure **2-Step Verification** is enabled on the Google Account.
  3. Verify port `587` with TLS is not blocked by a local firewall.
  4. Run the email test: `& "C:\xampp\php\php.exe" test_mail.php`

### Q2: MySQL database connection error (`Connection refused`).
- **Cause:** MySQL service is not running.
- **Solution:** Open XAMPP Control Panel → Start MySQL, or run:
  ```powershell
  & "C:\xampp\mysql_start.bat"
  ```

### Q3: An account is locked out during testing — reset it immediately.
- **Solution:**
  ```sql
  USE farmers_system_db;
  UPDATE users SET failed_logins = 0, lockout_tier = 0, lockout_until = NULL
  WHERE username = 'farmer_juan';
  ```

### Q4: How do I prove that contact numbers are AES-256 encrypted?
- **Solution:** Query the raw database column:
  ```powershell
  & "C:\xampp\mysql\bin\mysql.exe" -u root -e "USE farmers_system_db; SELECT username, contact_number_encrypted FROM users;"
  ```
  The contact number appears as an unintelligible base64 blob (*e.g., `WHlBpayMg+Pz70Nh...`*), proving plaintext is never stored.

### Q5: `git push` is rejected with "fetch first".
- **Cause:** Remote has commits your local branch doesn't have.
- **Solution:**
  ```powershell
  cd C:\xampp\htdocs\farmers_system
  git pull origin main --allow-unrelated-histories --no-edit
  git push -u origin main
  ```

### Q6: `git push` says "Permission denied to [old username]".
- **Cause:** Old GitHub credentials cached in Windows Credential Manager.
- **Solution:** Open **Control Panel → Credential Manager → Windows Credentials** → find `git:https://github.com` → Remove it → push again and sign in with `jerimetagalog`.

### Q7: Where is `farm_requests.php`? I can't find it.
- The farmer assistance portal is `assistance.php` (not `farm_requests.php`). Navigate to:
  ```
  http://localhost/farmers_system/assistance.php
  ```
  This page is only accessible after successful 2-step login.
