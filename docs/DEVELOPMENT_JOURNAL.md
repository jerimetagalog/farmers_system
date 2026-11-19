# DEVELOPMENT JOURNAL
**Course:** IT 107 – Information Assurance and Security 1  
**Project:** Secure Farmers Registration, Assistance and Monitoring System  
**Student:** Jerime Tagalog (`jerime.tagalog@csucc.edu.ph`)  
**Student ID:** 2020-1050  
**Security Settings:** Universal Section 2.3 Settings (MinUser:6, MaxUser:20, MinPwd:8, MaxName:50)  
**Deliverable Category:** Criteria P2 Compliance Document (Appendix C Format)

---

## Weekly Entry 1: Project Initialization & Checkpoint 1 Proposal
* **Period:** October 19, 2026 – October 25, 2026
* **Milestone Focus:** Checkpoint 1 Deliverables & Security Baseline Architecture

### 1. Objectives & Planning
The objective of this first week was to establish the architectural foundation and security parameters for the **Secure Farmers Registration, Assistance and Monitoring System** using the **Universal Security Settings** from Handout Section 2.3. All system thresholds are taken directly from the universal constants, ensuring complete compliance before writing application code.

### 2. Work Completed
- **Security Parameter Baseline (Universal Section 2.3):**
  - Minimum username length: **6 characters**
  - Maximum username length: **20 characters**
  - Minimum password length: **8 characters** (max 64)
  - Maximum length for each name field: **50 characters**
  - Forgot Password appearance threshold: after **2** consecutive failed login attempts
  - Progressive lockout tiers: Tier 1 = **15s**; Tier 2 = **30s**; Tier 3 = **60s**
  - OTP expiration window: **5 minutes** (300 seconds)
  - Inactive session timeout: **15 minutes** (warning at 14 minutes)
  - IP blocking threshold: **10** failures within 10 minutes
  - Security questions: farmer picks **3 different** questions from the Section 5 bank (Q1–Q8)
- **Repository & Secret Separation:**
  - Initialized Git repository on branch `main` linked to `https://github.com/jerimetagalog/farmers_system.git`.
  - Configured `.gitignore` to strictly exclude `config.php`, `vendor/`, `logs/`, and environment secrets.
  - Created `config.sample.php` providing sanitized environment placeholders without exposing real credentials.
- **SMTP Verification:**
  - Configured PHPMailer with Google SMTP (`smtp.gmail.com:587` with TLS).
  - Successfully dispatched a verified test OTP email to `jerime.tagalog@csucc.edu.ph`:
    ```
    C:\xampp\php\php.exe test_mail.php
    → SUCCESS: OTP 6-digit code sent to jerime.tagalog@csucc.edu.ph
    ```

### 3. Security Challenges & Solutions
- **Challenge:** Preventing accidental Git commits of SMTP app passwords and private keys during collaborative development.
- **Solution:** Enforced strict `.gitignore` rules before creating the local configuration file. Added pre-commit checks and documented safe configuration workflows in `docs/SEED_TABLE.md`.

---

## Weekly Entry 2: Database Schema, Cryptographic Architecture & Farmer Registration
* **Period:** October 26, 2026 – November 01, 2026
* **Milestone Focus:** Data Persistence, Field-Level Encryption & Input Validation

### 1. Objectives & Planning
The objective for Week 2 was designing a normalized MySQL relational schema capable of enforcing referential integrity and audit trails, while implementing field-level AES-256 encryption at rest and server-side validation.

### 2. Work Completed
- **Database Architecture (`database/schema.sql`):**
  - Created 8 relational tables: `users`, `authentication_answers`, `otps`, `password_history`, `ip_blocks`, `activity_logs`, `security_flags`, and `farm_requests`.
  - Configured PDO with `PDO::ATTR_EMULATE_PREPARES => false` and `PDO::ATTR_ERRMODE => ERRMODE_EXCEPTION` to guarantee true parameterized queries against SQL injection.
- **Cryptographic Protection at Rest (`php/security.php`):**
  - Implemented `aes256_encrypt()` and `aes256_decrypt()` using `AES-256-CBC` via PHP OpenSSL.
  - Enforced per-record 16-byte cryptographically random initialization vectors (`openssl_random_pseudo_bytes(16)`). Contact numbers are encrypted before storing in `users.contact_number_encrypted`.
- **Image CAPTCHA Module (`php/captcha.php` & `php/captcha_image.php`):**
  - Built a standalone GD image CAPTCHA generator rendering randomized alphanumeric characters with distortion lines and noise dots.
  - CAPTCHA layout: image + Refresh Code button on top row; full-width text input on second row.
  - Built single-use verification logic that unsets `$_SESSION['captcha_phrase']` immediately after evaluation, preventing replay attacks.
- **Server-Side Validation Engine (`php/validation.php`):**
  - Programmed rigorous validators matching the exact error strings from Section 4.10.
  - Enforced title casing, double-space rejections, 3-consecutive identical letter checks, and exact name length cap (50 characters, Universal Section 2.3).
  - Integrated 4 farmer-specific registration fields: Contact Mobile Number (AES-256 encrypted), Farm Type, Preferred Support Program, and Emergency Contact Number.

### 3. Security Challenges & Solutions
- **Challenge:** In PHP PDO, default configuration often enables emulated prepares, which can leave edge cases vulnerable to multi-byte SQL injection.
- **Solution:** Explicitly disabled emulation (`PDO::ATTR_EMULATE_PREPARES => false`) in `php/db.php`, ensuring that the MySQL server compiles queries separately from user input parameters.

---

## Weekly Entry 3: Authentication, Progressive Lockouts, 2-Step OTP & Password Recovery
* **Period:** November 02, 2026 – November 08, 2026
* **Milestone Focus:** Authentication Security, Brute-Force Defense & Recovery Flow

### 1. Objectives & Planning
The goal for Week 3 was implementing a resilient 2-step authentication flow, defending against automated credential stuffing via multi-tiered lockouts, and securing account recovery workflows.

### 2. Work Completed
- **2-Step Authentication Flow (`login.php` & `verify_otp.php`):**
  - Implemented primary credential verification (username, password, and CAPTCHA).
  - Configured login OTP generation using `random_int(100000, 999999)` with Bcrypt hashing and 5-minute expiration (Universal Section 2.3).
  - Prevented premature session initialization: authenticated session is only established after successful OTP entry.
- **Progressive Account Lockouts & IP Blocking (`php/auth.php`):**
  - Programmed lockout tiers (Universal Section 2.3): 15s after 3 failures, 30s after 6 failures, 60s after 9 failures.
  - Implemented IP blocking: automated blocking for 10 minutes when an IP accumulates 10 failed attempts within 10 minutes.
  - Linked the "Forgot Password" hyperlink to only appear after 2 consecutive failed login attempts.
- **Account Recovery & Password History (`forgot_password.php` & `change_password.php`):**
  - Verified user-selected security questions against normalized one-way hashes (`authentication_answers`).
  - Dispatched password reset OTPs with 5-minute expiry.
  - Built direct URL protection: `change_password.php` requires a one-time 32-byte cryptographic token passed via URL and validated against session state (`hash_equals()`). Direct URL navigation is rejected.
  - Enforced password history checks preventing reuse of the user's last 3 passwords (`password_history`).
  - Automated security email alerts notifying farmers of lockouts and successful password modifications.

### 3. Security Challenges & Solutions
- **Challenge:** Preventing timing attacks during password reset token and security answer verification.
- **Solution:** Replaced loose string comparisons with constant-time comparison functions (`hash_equals()`) and Bcrypt password verification (`password_verify()`).

---

## Weekly Entry 4: Farmer Portals, Admin Monitoring Dashboard & Security Verification
* **Period:** November 09, 2026 – November 15, 2026
* **Milestone Focus:** Access Control, Real-Time Monitoring & Project Deliverables

### 1. Objectives & Planning
The final week focused on completing farmer assistance request management, developing the administrator security monitoring dashboard, writing the CLI security monitor, conducting automated testing, and producing project documentation.

### 2. Work Completed
- **Farmer Assistance Request Portal (`assistance.php`):**
  - Built assistance request interface with support category routing (fertilizer subsidies, seed provision, crop protection, equipment loans, soil advisory).
  - Enforced strict authorization: farmers can only select and view their own requests (`WHERE farmer_id = :uid`).
  - Decrypted contact numbers at runtime using AES-256 for authorized display.
- **Administrator Security Monitoring Dashboard (`admin_dashboard.php` & `admin_api.php`):**
  - Built protected dashboard restricted to `role = 'admin'`.
  - Implemented 4 summary metric cards (Failed Logins Today, Active Lockouts, Active IP Blocks, Flagged Suspicious Events).
  - Built an interactive CSS/SVG 24-hour failed login bar chart.
  - Displayed top 5 offending IP addresses, active security flags, and the recent 50 immutable activity logs with event-type filtering.
  - Zero edit or delete capabilities provided on audit logs (enforcing non-repudiation).
- **Automated CLI Monitoring Script (`scripts/monitor.php`):**
  - Implemented 4 automated detection patterns:
    1. Rapid consecutive login failures (≥ 5 within 5 minutes).
    2. Off-hours administrator logins (between 10:00 PM and 6:00 AM).
    3. Active IP blocks requiring administrative review.
    4. Multi-account credential stuffing from a single IP (≥ 3 distinct usernames).
  - Tested script execution from terminal; verified duplicate flag suppression and successful console reporting.
- **Automated Self-Test Suite (`tests/self_test.php`):**
  - Created and executed 8 automated security tests:
    1. Database encryption & Bcrypt hashing at rest (PASSED).
    2. SQL injection resistance with malicious payloads (PASSED).
    3. XSS escaping via `htmlspecialchars()` (PASSED).
    4. Exact Section 4.10 error message string compliance (PASSED).
    5. Password history refusal (PASSED).
    6. Universal lockout duration timing (PASSED).
    7. Universal security parameter integrity (PASSED).
    8. Immutability of activity logs (PASSED).
  - Command used: `C:\xampp\php\php.exe tests/self_test.php`
  - Result: **Self-Test Summary: 8 Passed, 0 Failed** ✅
- **UI Redesign:**
  - **Home page (`index.php`):** Real farmer landscape background photo (Guilin karst rice fields), dark overlay at ~40% opacity for readability, gold gradient hero title, animated tabbed showcase (Support Programs / Security Architecture / Crop Advisory), 2026 subsidy allocation progress bars.
  - **Login page (`login.php`):** Split-panel layout — left dark panel (amber badges, feature highlights) + right white form panel. CAPTCHA: 2-row layout (image + Refresh Code button on top; full-width input below).
  - **Registration page (`register.php`):** 5-step wizard (Step 1: Personal Info → Step 2: Address → Step 3: Farm & Contact → Step 4: Credentials → Step 5: Security & Consent). Animated stepper header, per-step client-side validation.
- **Documentation Deliverables:**
  - Completed `docs/THREAT_MODEL.md` (Appendix B CIA matrix).
  - Completed `docs/DESIGN_DECISIONS.md` (10 "Your Call" justifications).
  - Completed `docs/PRIVACY_NOTE.md` (RA 10173 compliance).
  - Completed `docs/ERROR_MESSAGE_MAPPING.md` (Criteria P6 mapping).
  - Completed `docs/SYSTEM_USER_GUIDE.md` and repository `README.md`.

### 3. Security Challenges & Solutions
- **Challenge:** Ensuring the CLI monitoring script cannot be triggered by unauthenticated web visitors over HTTP.
- **Solution:** Added a strict execution environment check at the beginning of `scripts/monitor.php` (`php_sapi_name() === 'cli'`) and blocked web access via `scripts/.htaccess` (`Require all denied`). Direct web requests return an HTTP 403 Forbidden.
