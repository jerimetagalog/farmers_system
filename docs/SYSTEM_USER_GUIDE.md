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
14. [Troubleshooting & Frequently Asked Questions](#14-troubleshooting--frequently-asked-questions)

---

## 1. Pre-Flight Checklist & System Startup

Before launching the web portal or presenting to your instructor, ensure the local environment is running:

### Step 1.1: Launch XAMPP Services
1. Open the **XAMPP Control Panel** on your Windows workstation.
2. Click **Start** on **Apache** (Default Port: `80` or `8080`).
3. Click **Start** on **MySQL** (Default Port: `3306`).
4. Ensure both services display a green indicator status.

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

---

## 2. Quick Reference Demo Credentials

The system comes pre-seeded with 3 active, fully verified test accounts. All emails route to your real Gmail inbox (`jerime.tagalog@csucc.edu.ph`) using Gmail plus-addressing, allowing live verification of OTPs and alert notifications during demonstration.

| Account Role | Username | Plaintext Password | Registered Email | Contact Number (Encrypted AES-256) |
| :--- | :--- | :--- | :--- | :--- |
| **Agricultural Administrator** | `admin_monitor` | `Admin@2026!` | `jerime.tagalog@csucc.edu.ph` | `09123456789` |
| **Farmer 1 (Juan)** | `farmer_juan` | `JuanFarmer2026!` | `jerime.tagalog+juan@csucc.edu.ph` | `09876543210` |
| **Farmer 2 (Maria)** | `farmer_maria` | `MariaFarmer2026!` | `jerime.tagalog+maria@csucc.edu.ph` | `09234567891` |

### Assigned Security Questions & Answers
- **Question 1:** *"What was the name of your first elementary school?"*  
  **Answer:** `bantay`
- **Question 2:** *"What is the name of the street you grew up on?"*  
  **Answer:** `rizal street`
- **Question 3:** *"What was your childhood nickname?"*  
  **Answer:** `jemboy`

---

## 3. Module 1: Portal Navigation & Landing Page

### Purpose
Provides a secure landing portal welcoming farmers and agricultural staff while establishing session state and enforcing HTTP security headers (`X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`).

### UI Overview (New Design):
- **Background:** Real farmer landscape photo (Guilin karst mountains & rice fields) with a ~40% dark overlay for readability.
- **Hero Text:** Gold-gradient title "*Secure Farmers Registration, Assistance and Monitoring System*" with text-shadow for contrast.
- **Tabbed Showcase:** Three interactive tabs — *Support Programs*, *Security Architecture*, *Crop Advisory*.
- **Right Sidebar:** 2026 Subsidy Allocation progress bars and Municipal Helpdesk contact card.

### Step-by-Step Walkthrough:
1. Open `http://localhost/farmers_system/index.php`.
2. Notice the navigation layout:
   - **System Prospect Banner:** *"Secure Farmers Registration, Assistance and Monitoring System | 2020-1050"* displayed strictly at the top.
   - **Top-Right Navigation:** For unauthenticated visitors, only **"Home"**, **"Register"**, and **"Sign In"** links appear. **Logout is never displayed to a guest**.
   - **Hero Actions:** Two primary buttons: **"Register as Farmer"** and **"Sign In"**.
   - **Footer:** Standardized copyright with student ID `2020-1050`.

---

## 4. Module 2: Farmer Registration Flow (5-Step Wizard)

### Purpose
Allows new farmers to create an account through a **5-step wizard** (no scrolling needed), strictly enforcing Section 4 & 5 validation rules, calculating real-time age, generating a CAPTCHA challenge, and capturing informed consent under RA 10173.

### Wizard Steps:
| Step | Title | Fields |
| :---: | :--- | :--- |
| **1** | Personal Info | ID Number, First/Middle/Family Name, Name Extension, Birthdate, Age |
| **2** | Address | Purok/Street, Barangay, Municipality, Province, Country, ZIP Code |
| **3** | Farm & Contact | Email, Contact Mobile Number, Farm Type, Support Program, Emergency Contact |
| **4** | Credentials | Username, Password, Confirm Password |
| **5** | Security & Consent | 3 Security Questions + Answers, CAPTCHA, Privacy Consent |

### Step-by-Step Walkthrough:
1. Click **"Register"** in the top navigation or navigate to `register.php`.
2. The page opens on **Step 1 — Personal Info**. Fill:
   - **ID Number:** Format `YYYY-NNNN` (*e.g., `2026-9001`*). Duplicate IDs are rejected.
   - **First Name:** Letters and spaces only, Title Case (*e.g., `Gabriel`*). Max 50 characters. Numbers, special characters, and ALL CAPS are blocked.
   - **Middle Name:** Optional. If entered, Title Case is required. Max 50 characters.
   - **Family Name:** Required, Title Case (*e.g., `Mendoza`*). Max 50 characters.
   - **Name Extension:** Optional dropdown (*`Jr.`, `Sr.`, `II`, `III`, `IV`*).
   - **Birthdate:** Select a date at least 18 years in the past.
   - **Age:** Auto-calculated by JavaScript upon selecting birthdate; server validates independently.
   - Click **"Next Step →"** (client-side validates Step 1 before allowing progression).
3. **Step 2 — Address:**
   - **Purok / Street:** Alphanumeric, 3–50 characters.
   - **Barangay, Municipality, Province, Country:** Title Case, letters and spaces only, 2–50 characters each.
   - **ZIP Code:** Exactly 4 numeric digits (*e.g., `8600`*).
   - Click **"Next Step →"**.
4. **Step 3 — Farm & Contact:**
   - **Email Address:** Valid email (*e.g., `jerime.tagalog+demo@csucc.edu.ph`*).
   - **Contact Number (Required):** Exactly 11 digits starting with `09`. Encrypted at rest using AES-256-CBC.
   - **Farm Type (Required Dropdown):** `Rice`, `Corn`, `Vegetable`, `Fruit`, or `Mixed`.
   - **Support Program (Required Dropdown):** `Fertilizer Subsidy`, `Seed Provision`, `Crop Protection`, `Equipment Loan`, or `Soil Advisory`.
   - **Emergency Contact Number (Optional):** If supplied, must be 11 digits starting with `09`.
   - Click **"Next Step →"**.
5. **Step 4 — Credentials:**
   - **Username:** Starts with a letter, alphanumeric only. Length between **6 and 20** characters (Universal Section 2.3). Live availability check verifies uniqueness.
   - **Password:** Minimum **8 characters** (Universal Section 2.3). Live password strength visualizer checks uppercase, lowercase, number, and special character.
   - **Confirm Password:** Must match password exactly.
   - Click **"Next Step →"**.
6. **Step 5 — Security & Consent:**
   - **Select and Answer 3 Different Security Questions** from the Section 5 bank (Q1–Q8). Stored as one-way Bcrypt hashes.
   - **GD Image CAPTCHA:** 2-row layout — image + "Refresh Code" button on top; full-width text input on second row. Type the 6-character code shown.
   - **Privacy Notice Checkbox:** Review and check *"I agree to the collection and processing of my personal information in accordance with RA 10173"*.
   - Click **"Complete Registration"**.
7. **Expected Result:**
   - Server validates all constraints independently of the client.
   - Password and security answers are hashed using Bcrypt.
   - Contact number is encrypted using AES-256-CBC with a unique 16-byte random IV.
   - A 6-digit OTP is generated using `random_int()`, hashed into `otps`, and dispatched to the farmer's email.
   - Browser redirects to `verify_otp.php`.

---

## 5. Module 3: Registration Email OTP Verification

### Purpose
Guarantees that an account cannot be logged into until the owner proves ownership of the registered email address via a time-limited out-of-band OTP.

### Step-by-Step Walkthrough:
1. The browser lands on `verify_otp.php`.
2. Notice the security indicators:
   - Purpose displayed: *"Email Verification for Registration"*.
   - Timer countdown showing the **5-minute expiration window** (Universal Section 2.3).
   - Attempt counter showing *"Attempt 1 of 3"*.
3. Open your Gmail inbox (`jerime.tagalog@csucc.edu.ph`).
4. Open the email titled: **"Your Verification Code - Farmers System"**.
5. Copy the 6-digit code.
6. Return to `verify_otp.php`, enter the 6 digits, and click **"Verify Code"**.
7. **Expected Result:**
   - The server verifies the OTP hash against `otps`.
   - The user's account is activated (`users.is_verified = 1`).
   - The single-use OTP is marked as used (`is_used = 1`).
   - A success banner appears: *"Email verified successfully! You may now sign in."*
   - Browser redirects to `login.php`.

---

## 6. Module 4: Farmer Two-Step Authentication (Login)

### Purpose
Implements multi-factor authentication requiring username, password, CAPTCHA verification, and a one-time out-of-band email OTP before establishing a session.

### UI Overview (New Split-Panel Design):
- **Left Panel (Dark):** Dark brown/amber background with farm photo overlay. Shows system name, security feature highlights with amber icon badges.
- **Right Panel (White):** Clean white form with labeled inputs (labels strictly above inputs), password Show/Hide toggle, and 2-row CAPTCHA.

### Step-by-Step Walkthrough:
1. Navigate to `login.php`.
2. **Step 1: Primary Authentication:**
   - **Username:** `farmer_juan`
   - **Password:** `JuanFarmer2026!`
   - **CAPTCHA:** Enter the code shown in the top CAPTCHA image; type it in the full-width input below.
   - Click **"Sign In"**.
3. **Expected Result (Step 1):**
   - The system checks username and verifies password hash using `password_verify()`.
   - **Session is NOT established yet** (defense against premature session creation).
   - A new 6-digit Login OTP is generated, hashed into `otps`, and emailed to `jerime.tagalog+juan@csucc.edu.ph`.
   - Browser redirects to `verify_otp.php`.
4. **Step 2: Second Factor Verification:**
   - Check your Gmail inbox for the **"Your Login Verification Code"** email.
   - Enter the 6-digit OTP into `verify_otp.php` and submit.
5. **Expected Result (Step 2):**
   - Server validates the OTP hash and verifies 5-minute expiration.
   - Session ID is regenerated (`session_regenerate_id(true)`) to prevent session fixation attacks.
   - Role-based redirect sends Farmer Juan to `farm_requests.php`.
   - The navigation header now displays: **"Hello, Juan!"**, **"My Requests"**, and **"Sign Out"**.

---

## 7. Module 5: Farm Assistance Request Management

### Purpose
Provides self-service agricultural support request submission, status tracking, and on-the-fly decryption of farmer contact details, while strictly enforcing IDOR prevention.

### Step-by-Step Walkthrough:
1. Land on `farm_requests.php`.
2. **Review Farmer Information Card:**
   - Shows Juan's full name, student ID, and registered email.
   - **Decrypted Contact Number:** `09876543210` is displayed legibly. In MySQL, this value is stored as an encrypted AES-256 base64 blob (`users.contact_number_encrypted`). The server decrypted it on the fly using the server-side key and per-record IV.
3. **Review Existing Requests Table:**
   - Displays all assistance requests registered to Farmer Juan.
   - Columns: Support Program, Date Submitted, Farm Type, Status badge (*Pending*, *Approved*, *Dispatched*, *Rejected*).
4. **Submit a New Assistance Request:**
   - **Support Program:** Select `'Fertilizer Subsidy'` (or `'Seed Provision'`, `'Crop Protection'`, etc.).
   - **Farm Type:** Select `'Rice'`.
   - **Farm Location / Notes:** Type details (*e.g., "Barangay Ampayon, 2 hectare paddy rice farm"*).
   - Click **"Submit Request"**.
5. **Expected Result:**
   - Input is validated and saved to `farm_requests`.
   - The new request immediately appears in the table with a yellow `Pending` badge.
   - An audit log event (`REQUEST_SUBMITTED`) is recorded in `activity_logs`.
6. **Anti-IDOR Verification (Instructor Defense):**
   - Point out that the SQL query is strictly scoped: `SELECT * FROM farm_requests WHERE farmer_id = :session_user_id`.
   - Even if Juan tampers with browser developer tools or URL query parameters, he can never read or modify Maria's requests.

---

## 8. Module 6: Idle Session Timeout & Auto-Logout

### Purpose
Protects farmer data on unattended terminals by terminating inactive sessions after the Universal timeout window (**15 minutes**; warn at 14 minutes).

### Step-by-Step Walkthrough:
1. Stay on any authenticated page (*e.g., `farm_requests.php` or `admin_dashboard.php`*).
2. The client script `js/idle_timer.js` monitors user activity (mouse movement, keypresses, clicks, touches).
3. **Warning Phase (At 14 minutes):**
   - An accessible modal overlay pops up: *"Session Expiring Soon: You have been inactive for 14 minutes. You will be logged out in 60 seconds."*
   - Two buttons are provided: **"Stay Logged In"** and **"Log Out Now"**.
   - If the user clicks "Stay Logged In", an asynchronous keep-alive ping is sent to `api_keep_alive.php`, resetting both the client timer and the PHP session timestamp.
4. **Expiration Phase (At 15 minutes):**
   - Browser is redirected to: `logout.php?reason=timeout`.
   - The PHP session is completely wiped (`session_unset()`, `session_destroy()`), and the session cookie is deleted with an expired timestamp.
   - Browser lands on `login.php?timeout=1` displaying: *"Your session expired due to 15 minutes of inactivity. Please log in again."*

---

## 9. Module 7: Account Recovery & Password History

### Purpose
Enables legitimate users to recover access without administrator intervention using their security questions, while preventing the reuse of recent passwords (Protection 7).

### Step-by-Step Walkthrough:
1. Go to `login.php`.
2. Click the **"Forgot Password?"** link (appears after 2 consecutive failed login attempts).
3. **Step 1: Identity Challenge:**
   - **Username:** `farmer_maria`
   - **CAPTCHA:** Solve the image CAPTCHA.
   - Click **"Continue"**.
4. **Step 2: Security Question Verification:**
   - System displays the security question options assigned to the user.
   - Select **Question 1**: *"What was the name of your first elementary school?"*
   - Type the answer: `bantay` *(case-insensitive)*.
   - Click **"Verify Answer"**.
5. **Expected Result (Step 2):**
   - The server normalizes the answer and verifies it against the Bcrypt hash in `authentication_answers`.
   - A 6-digit **Password Reset OTP** is dispatched to `jerime.tagalog+maria@csucc.edu.ph`.
6. **Step 3: Reset OTP Verification:**
   - Retrieve the 6-digit code from your email and enter it on `verify_otp.php`.
   - Submit the code.
7. **Step 4: Token-Protected Password Reset (`change_password.php`):**
   - The server generates a random 32-byte cryptographic token and redirects to `change_password.php?token=<token>`.
   - **Demonstrate Protection S5 (Direct URL Protection):** Copy the URL and try opening it in an Incognito window without the session. The server rejects direct access and redirects to `login.php`.
8. **Step 5: Demonstrate Password History Prevention (Protection 7):**
   - Attempt to reuse Maria's previous password: `MariaFarmer2026!`.
   - Click **"Update Password"**.
   - **Rejection Message:** *"You cannot reuse any of your last 3 passwords. Please choose a different password."*
9. **Step 6: Submit a New Valid Password:**
   - Enter a brand new password: `NewMariaSecret!2026`.
   - Confirm the password and submit.
10. **Expected Result:**
    - The new password is saved as a Bcrypt hash in `users` and recorded in `password_history`.
    - Any existing active sessions for this account are invalidated.
    - A **Security Alert Email** is sent informing the user of the successful password update with timestamp, IP address, and browser information.

---

## 10. Module 8: Progressive Account Lockout & Rate Limiting

### Purpose
Thwarts automated brute-force attacks by enforcing escalating lockout tiers (Universal Section 2.3) and blocking abusive IP addresses.

### Step-by-Step Walkthrough:
1. Open a new Incognito / Private browser window.
2. Navigate to `login.php`.
3. Enter **Username:** `farmer_juan`.
4. Enter an **incorrect password** (*e.g., `WrongPass123!`*).
5. Solve the image CAPTCHA and click **"Sign In"**.
6. Repeat this failure 3 consecutive times:
   - **Attempt 1:** Generic error: *"Invalid username or password."* (Failure count = 1).
   - **Attempt 2:** Generic error: *"Invalid username or password."* (Failure count = 2). Notice that the **"Forgot Password?"** link appears right after Attempt 2!
   - **Attempt 3:** **Lockout Tier 1 Activated!** (**15 seconds** cooldown — Universal Section 2.3).
7. **Show the Instructor What Happens on Failure 3:**
   - A live countdown timer appears on screen: *"Account is locked. Please wait 15 seconds."*
   - The **"Sign In"** button is disabled.
   - Check your Gmail inbox: A **Lockout Security Alert Email** has arrived.
   - Refresh the page: explain that refreshing cannot bypass the lockout because the server enforces `lockout_until` in MySQL.
8. **Subsequent Lockout Tiers:**
   - **Tier 2 (6 failures):** **30 seconds** lockout.
   - **Tier 3 (9 failures):** **60 seconds** lockout.
9. **IP-Level Blocking Defense:**
   - If 10 failures occur from the same IP address within 10 minutes (Universal Section 2.3), the server inserts an IP ban into `ip_blocks` for 10 minutes.
   - All subsequent requests from that IP address are blocked with:  
     *"Access temporarily blocked due to repeated security failures. Please contact the agricultural administrator."*

---

## 11. Module 9: Administrator Security Monitoring Dashboard

### Purpose
Provides authorized agricultural administrators with a centralized, read-only operational dashboard to inspect security events, identify brute-force patterns, review active blocks, and audit immutable system logs.

### Step-by-Step Walkthrough:
1. Navigate to `login.php`.
2. Enter the administrator credentials:
   - **Username:** `admin_monitor`
   - **Password:** `Admin@2026!`
   - **CAPTCHA:** Enter image CAPTCHA.
3. Submit and enter the **Login OTP** dispatched to `jerime.tagalog@csucc.edu.ph`.
4. The system navigates to `admin_dashboard.php`.
5. **Inspect the 4 Summary Metric Cards:**
   - **Failed Logins (Today):** Total failed authentication attempts in the last 24 hours.
   - **Active Account Lockouts:** Accounts currently in an active progressive lockout cooldown.
   - **Active IP Blocks:** IP addresses currently banned due to rate-limit violations.
   - **Security Flags (Unresolved):** Suspicious security anomalies flagged by the automated monitoring script.
6. **Inspect the 24-Hour Failed Login Bar Chart:**
   - Responsive CSS/SVG bar chart visualizing failed login attempts broken down by hour of the day.
7. **Inspect Top 5 Offending IPs Table.**
8. **Inspect Active Security Monitoring Flags.**
9. **Inspect the Immutable Audit Logs Table (Protection S11):**
   - Displays the 50 most recent events in `activity_logs`.
   - Columns: Timestamp, Event Type badge, Username, User ID, IP Address, Browser User Agent, Details.
   - **Non-Repudiation Defense:** There are **NO edit or delete buttons** on the dashboard. Database privileges prevent alteration of audit records.

---

## 12. Module 10: Automated CLI Intrusion Detection Script

### Purpose
Runs as a background scheduled process (Windows Task Scheduler or Cron) to analyze activity logs and detect suspicious cybersecurity attack patterns.

### The 4 Detection Patterns:
1. **Rapid Consecutive Failures:** Detects ≥ 5 failed login attempts from a single IP or username within a 5-minute sliding window.
2. **Off-Hours Administrator Logins:** Detects administrator logins occurring between 10:00 PM and 6:00 AM.
3. **Active IP Blocks:** Flags IP addresses that have reached the IP blocking threshold.
4. **Multi-Account Credential Stuffing:** Detects when a single IP attempts logins across ≥ 3 distinct usernames within 10 minutes.

### Step-by-Step Walkthrough:
1. Open PowerShell in the project folder:
   ```powershell
   cd C:\xampp\htdocs\farmers_system
   ```
2. Execute the monitoring script:
   ```powershell
   & "C:\xampp\php\php.exe" scripts/monitor.php
   ```
3. **Review the Output in Terminal:**
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
4. **Demonstrate Duplicate Suppression:** Run the script a second time. `Duplicate flags ignored: 1` proves flags are not spammed.
5. **Demonstrate Browser Execution Denial:** Open `http://localhost/farmers_system/scripts/monitor.php` in a web browser. The server returns **HTTP 403 Forbidden**.

---

## 13. Module 11: Automated Security Self-Test Suite

### Purpose
Runs a comprehensive automated test suite verifying that all 13 protections, cryptographic algorithms, error message strings, and database immutability constraints are functioning properly.

### Step-by-Step Walkthrough:
1. Open PowerShell in the project directory:
   ```powershell
   cd C:\xampp\htdocs\farmers_system
   ```
2. Run the self-test suite:
   ```powershell
   & "C:\xampp\php\php.exe" tests/self_test.php
   ```
3. **Verify the 8 Test Assertions:**
   - **Test 1:** Database encryption & Bcrypt hashing at rest (PASSED).
   - **Test 2:** SQL injection resistance with `' OR '1'='1` (PASSED).
   - **Test 3:** Cross-Site Scripting (XSS) escaping via `htmlspecialchars()` (PASSED).
   - **Test 4:** Core registration error messages match Section 4.10 verbatim (PASSED).
   - **Test 5:** Password history refusal blocking reuse of the last 3 passwords (PASSED).
   - **Test 6:** Universal progressive lockout tier durations (15s, 30s, 60s) (PASSED).
   - **Test 7:** Universal security parameter integrity (MinUser:6, MaxUser:20, MinPwd:8) (PASSED).
   - **Test 8:** Activity log immutability with zero `UPDATE`/`DELETE` queries (PASSED).
4. **Expected Output:**
   ```
   -----------------------------------------------------------------
   Self-Test Summary: 8 Passed, 0 Failed
   =================================================================
   ```

---

## 14. Troubleshooting & Frequently Asked Questions

### Q1: I am not receiving OTP or alert emails in my inbox.
- **Cause:** Incorrect Google App Password or SMTP port blocked.
- **Solution:**
  1. Verify your 16-character Google App Password in `config.php` for `jerime.tagalog@csucc.edu.ph`.
  2. Ensure 2-Step Verification is enabled on the Google Account.
  3. Verify port `587` with TLS is open and not blocked by firewall software.
  4. Test email delivery: `& "C:\xampp\php\php.exe" test_mail.php`

### Q2: MySQL database connection error (`Connection refused`).
- **Cause:** MySQL service is stopped.
- **Solution:** Open XAMPP Control Panel and click **Start** on MySQL, or run:
  ```powershell
  & "C:\xampp\mysql_start.bat"
  ```

### Q3: An account is locked out during testing and I need to clear it immediately.
- **Solution:** Run the following SQL query in MySQL to reset the failure counter:
  ```sql
  USE farmers_system_db;
  UPDATE users SET failed_logins = 0, lockout_tier = 0, lockout_until = NULL WHERE username = 'farmer_juan';
  ```

### Q4: How do I demonstrate that contact numbers are really encrypted in the database?
- **Solution:** Run this query to show your instructor the raw ciphertext:
  ```powershell
  & "C:\xampp\mysql\bin\mysql.exe" -u root -e "USE farmers_system_db; SELECT username, contact_number_encrypted FROM users;"
  ```
  The contact number will appear as a scrambled base64 string (*e.g., `WHlBpayMg+Pz70Nh...`*), proving that the number is never stored in readable plaintext.

### Q5: How do I run the email test to confirm SMTP is working?
- **Solution:**
  ```powershell
  & "C:\xampp\php\php.exe" test_mail.php
  ```
  Expected output: `SUCCESS: OTP code sent to jerime.tagalog@csucc.edu.ph`
