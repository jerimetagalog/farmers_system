# CARAGA STATE UNIVERSITY &middot; BS INFORMATION TECHNOLOGY
## IT 107 &middot; Information Assurance and Security 1

# Final Laboratory Project
### Secure Registration, Authentication and Monitoring System
**STUDENT HANDOUT &middot; 1st Semester A.Y. 2026&ndash;2027**

---

| Student Submission Details | |
| :--- | :--- |
| **Name** | Jerime Tagalog |
| **Student No.** | 2020-1050 |
| **Universal Security** | Handout Section 2.3 Universal Settings |
| **My System** | Secure Farmers Registration, Assistance and Monitoring System |
| **Approved by** | |
| **Date approved** | |

---

# APPENDIX A: SYSTEM PROPOSAL FORM

| Item | Details |
| :--- | :--- |
| **System Name** | **Secure Farmers Registration, Assistance and Monitoring System** |
| **Who Uses It** | **Farmers & Agricultural Producers**: Register, verify their email via OTP, log in using two-step authentication, submit applications for agricultural support programs (fertilizer and seed subsidies, crop protection, equipment loans, soil advisory), track application status, utilize self-service password recovery, and safely terminate sessions.<br><br>**Agricultural Administrative Officers**: Authenticate via two-step verification, review and process municipal farm support applications, update allocation statuses (Pending, Approved, Dispatched, Rejected), and access the security monitoring dashboard to inspect authentication logs, progressive lockouts, IP blocks, and automated threat flags. |
| **What It Stores** | 1. **Farmer Account & Farm Identity Data**: ID Number (`2020-1050` format), First Name, Middle Name, Family Name, Name Extension, Birthdate, Age (server verified $\ge 18$), Permanent Farm Address (Purok/Street, Barangay, Municipality/City, Province, Country, ZIP Code), Email Address, and Username.<br>2. **Authentication & Security Data**: Bcrypt/Argon2 password hashes, hashed answers for 3 chosen security questions, hashed OTP codes (purpose, expiry, attempt counter), password history (last 3 hashes), and timestamped RA 10173 data privacy consent.<br>3. **Agricultural Program Data**: Support category, target implementation date, farm parcel location, crop requirements, status, and AES-256 encrypted contact numbers.<br>4. **Security Audit Logs**: Immutable append-only activity logs (event type, username typed, user ID, client IP, user agent, event details, timestamp), failed login tallies, progressive lockout timers, and automated monitoring flags. |
| **Why Accounts Need Protecting** | The system manages verified agricultural profiles, farmer identities, and municipal distribution subsidies governed by the **Philippine Data Privacy Act of 2012 (Republic Act No. 10173)**. Unauthorized access risks farmer identity theft, fraudulent reallocation of government seed and fertilizer vouchers, unauthorized cancellation of farm support requests, brute-force credential attacks, and administrative compromise. Sessions and inputs must be strictly safeguarded against SQL injection, Cross-Site Scripting (XSS), Cross-Site Request Forgery (CSRF), and unauthorized privilege escalation. |
| **Sensitive Field to Encrypt (Protection 13)** | **Farmer Contact Mobile Number**.<br>Encrypted at rest in the database using **AES-256** (via OpenSSL with AES-256-CBC and cryptographically secure random IVs). The plaintext contact number is decrypted only when displayed to its authenticated owner or authorized municipal agricultural officers. The cryptographic key is isolated in configuration files outside version control. |

---

### System-Specific Registration Fields
*(At least four fields, of at least three different kinds, each with explicit rules and error messages)*

| # | Field Name | Kind / Data Type | Required? | Validation Rule | Exact Error Message |
| :-: | :--- | :--- | :-: | :--- | :--- |
| **1** | **Contact Mobile Number** | Formatted text / Phone | **Required** | Valid Philippine mobile format: exactly 11 digits starting with `09` (`09XXXXXXXXX`). Encrypted at rest in database using AES-256 (Protection 13). | `Enter a valid contact number in the format 09XXXXXXXXX.` |
| **2** | **Farmer Classification** | Dropdown / Select | **Required** | Must be selected from authorized categories: `Smallholder Farmer`, `Tenant Farmer`, `Commercial Farmer`, `Agricultural Worker`. Server-side validation against whitelist. | `Select a valid farmer type.` |
| **3** | **Primary Crop / Commodity** | Dropdown / Select | **Required** | Must match an authorized agricultural commodity: `Rice`, `Corn`, `Coconut`, `Banana`, `Vegetables`, `High-Value Crops`. Server rejects unlisted options. | `Select a valid primary crop category.` |
| **4** | **Emergency Contact Number** | Formatted text / Phone | **Optional** | Optional. If provided, must strictly conform to Philippine mobile format: exactly 11 digits starting with `09`. | `Enter a valid emergency contact number in the format 09XXXXXXXXX.` |

---

### Universal Security Configuration (Handout Section 2.3)

| Security Parameter | Standard Universal Value |
| :--- | :--- |
| **Minimum Username Length** | 6 characters |
| **Maximum Username Length** | 20 characters |
| **Minimum Password Length** | 8 characters (max 64) |
| **Maximum Length of Name Fields** | 50 characters |
| **Failed Logins before Forgot Password appears** | 2 failed attempts |
| **Lockout Tier 1 (3 failed attempts)** | 15 seconds |
| **Lockout Tier 2 (6 failed attempts)** | 30 seconds |
| **Lockout Tier 3 (9 failed attempts)** | 60 seconds |
| **Email OTP Expiry** | 5 minutes (300 seconds) |
| **Idle Session Timeout** | 15 minutes (warning at 14 minutes) |
| **IP Block Threshold** | 10 failures in 10 minutes |
| **Authentication Question Selection** | 3 different questions selected from Section 5 bank |
