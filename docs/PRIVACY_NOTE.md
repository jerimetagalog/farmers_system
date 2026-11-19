# PRIVACY NOTE AND DATA PROTECTION COMPLIANCE
**Course:** IT 107 – Information Assurance and Security 1  
**Project:** Secure Farmers Registration, Assistance and Monitoring System  
**Student:** Jerime Tagalog (`jerime.tagalog@csucc.edu.ph`)  
**Student ID:** 2020-1050  
**Security Settings:** Universal Section 2.3 Settings  
**Legal Framework:** Republic Act No. 10173 (Philippine Data Privacy Act of 2012 / DPA)  
**Deliverable Category:** Criteria P5 Compliance Document

---

## 1. Executive Summary and Regulatory Statement
The **Secure Farmers Registration, Assistance and Monitoring System** collects, processes, and stores personal data to facilitate farmer identity verification, agricultural assistance program enrollment, municipal subsidy allocation, and security monitoring. In compliance with the **Philippine Data Privacy Act of 2012 (Republic Act No. 10173)**, its Implementing Rules and Regulations (IRR), and National Privacy Commission (NPC) circulars, this system strictly enforces the three core data privacy principles:

1. **Transparency:** Farmers are informed prior to registration through a clear, plain-language Privacy Notice detailing what data is gathered, why it is gathered, how it is secured, and how long it is retained.
2. **Legitimate Purpose:** Processing is strictly limited to farmer identification, agricultural assistance program enrollment, emergency contact management, and system security monitoring.
3. **Proportionality:** Only data strictly necessary for agricultural program delivery and identity assurance is collected. No unnecessary or invasive metadata is solicited.

---

## 2. Classification of Collected Data Elements (Section 3, RA 10173)

Under Section 3 of RA 10173, data is classified into **Personal Information (PI)** and **Sensitive Personal Information (SPI)**. The table below documents every data field collected during farmer registration, its statutory classification, and the technical necessity justifying its collection.

| Field Name | Category in System | Statutory Classification (RA 10173) | Purpose and Technical Necessity |
| :--- | :--- | :--- | :--- |
| **ID Number** | Core Registration | Personal Information (§3(g)) | Unique institutional identifier linking the user account to municipal agricultural records and subsidy allocation registers. |
| **First Name** | Core Registration | Personal Information (§3(g)) | Required for legal identity verification and matching farmer subsidy records. |
| **Middle Name** | Core Registration | Personal Information (§3(g)) | Distinguishes individuals sharing identical first and family names (common in Philippine naming conventions). |
| **Family Name** | Core Registration | Personal Information (§3(g)) | Legal identification and formal agricultural documentation. |
| **Name Extension** | Core Registration | Personal Information (§3(g)) | Clarifies generational titles (Jr., Sr., III) to avoid record mix-ups. |
| **Birthdate** | Core Registration | **Sensitive Personal Information** (§3(l)(1)) | Age verification: essential for validating legal age of consent (≥ 18) and municipal program eligibility. |
| **Age** | Core Registration | **Sensitive Personal Information** (§3(l)(1)) | Program eligibility validation, automated calculation from birthdate, and legal age-of-consent verification. |
| **Purok / Street** | Core Registration | Personal Information (§3(g)) | Granular farm location for subsidy delivery routing and agricultural outreach. |
| **Barangay** | Core Registration | Personal Information (§3(g)) | Local government unit demarcation for public agricultural program reporting. |
| **Municipality / City** | Core Registration | Personal Information (§3(g)) | Geographic routing and demographic analysis for municipal farm support allocation. |
| **Province** | Core Registration | Personal Information (§3(g)) | Regional agricultural administration mapping. |
| **Country** | Core Registration | Personal Information (§3(g)) | National agricultural residency verification. |
| **Zip Code** | Core Registration | Personal Information (§3(g)) | Postal validation and geographical zoning for logistics. |
| **Email Address** | Core Registration | Personal Information (§3(g)) | Out-of-band communication: delivery of 6-digit registration OTPs, login verification codes, password reset tokens, and security lockout alerts. |
| **Username** | Core Registration | Personal Information (§3(g)) | Primary authentication identifier for login and session state management. |
| **Password** | Core Registration | Authentication Secret | Primary secret verifying identity. Stored strictly as a Bcrypt one-way hash (`PASSWORD_DEFAULT`); never stored in plaintext. |
| **Security Answers** | Core Registration | Authentication Secret | Secondary authentication credential for account recovery. Stored strictly as Bcrypt one-way hashes after normalization. |
| **Contact Number** | Farmer-Specific | **Sensitive Personal Information** (§3(l)(1)) | Direct farmer communication channel for subsidy delivery notifications, farm advisory callbacks, and identity verification. Encrypted at rest using AES-256-CBC. |
| **Farm Type** | Farmer-Specific | Operational Metadata | Program routing: determines which agricultural support category and input types apply (e.g., Rice, Corn, Vegetable, Fruit, Mixed). |
| **Support Program** | Farmer-Specific | **Sensitive Personal Information** (§3(l)(2)) | Agricultural program routing: indicates the type of assistance sought (Fertilizer Subsidy, Seed Provision, Crop Protection, Equipment Loan, Soil Advisory), reflecting the farmer's economic needs and farm conditions. |
| **Emergency Contact Number** | Farmer-Specific | Personal Information (§3(g)) | Life-safety measure: emergency contact during on-farm accidents, natural disasters, or administrative emergencies. |
| **Assistance Request Date** | Request Module | Operational Agricultural Data | Program scheduling and municipal resource allocation. |
| **Farm Location / Notes** | Request Module | **Sensitive Personal Information** (§3(l)(2)) | Agricultural details disclosed by the farmer to enable officers to prepare appropriate support materials and delivery logistics. |

---

## 3. Data Protection and Security Countermeasures

To uphold integrity and confidentiality (§20, RA 10173), the following safeguards are implemented at the architectural and application layers:

### 3.1 Field-Level Cryptographic Encryption at Rest (Protection 13)
- **Target Field:** Farmer Contact Mobile Number (`contact_number_encrypted`).
- **Algorithm:** Advanced Encryption Standard (AES) with a 256-bit key in Cipher Block Chaining mode (`AES-256-CBC`).
- **Initialization Vector (IV):** A unique, cryptographically random 16-byte IV (`openssl_random_pseudo_bytes(16)`) is generated for every individual record, preventing pattern matching across identical contact numbers.
- **Key Management:** The 32-byte secret key is defined in `config.php`, located strictly outside the Git repository via `.gitignore`.
- **Contrast with Hashing:** Unlike passwords and security answers (which use one-way hashing because the plain values never need to be retrieved), the contact number must be reversibly decrypted by authorized agricultural officers and the farmer to coordinate subsidy delivery and farm advisory communications.

### 3.2 One-Way Cryptographic Hashing
- **Passwords:** Hashed with PHP `password_hash()` utilizing Bcrypt with a work factor cost of 10. Passwords are never sent via email or stored in session variables.
- **Security Question Answers:** Normalized (trimmed and lowercased) and hashed with Bcrypt before persistence in `authentication_answers`.
- **One-Time Passwords (OTPs):** Hashed with Bcrypt before saving to `otps`, ensuring database compromise does not expose live OTP codes.

### 3.3 Strict Access Control and Principle of Least Privilege
- **Role-Based Access Control (RBAC):** Distinct roles (`farmer` vs. `admin`) enforced by server-side session checks.
- **Direct Object Reference Prevention (IDOR):** Assistance request queries strictly bind `:session_user_id` to `farmer_id` in SQL statements (`WHERE farmer_id = :uid`), preventing users from accessing or tampering with other farmers' requests.
- **Admin Dashboard Isolation:** Only authenticated accounts with `role = 'admin'` can access `admin_dashboard.php` and `admin_api.php`. Unauthorized attempts trigger instant termination and security audit logging.

### 3.4 Transport and Browser-Level Protections
- **Cache Invalidation:** Sensitive pages issue HTTP headers:  
  `Cache-Control: no-store, no-cache, must-revalidate, max-age=0`  
  `Pragma: no-cache`  
  This prevents browser caching and history traversal attacks on shared computer terminals.
- **Clickjacking and MIME Sniffing Defense:** Server emits `X-Frame-Options: DENY` and `X-Content-Type-Options: nosniff`.
- **Session Protection:** Session cookies are configured with `HttpOnly = true`, `SameSite = 'Strict'`, and regenerated upon authentication.

---

## 4. Security Audit Logging and Privacy Boundaries

Section 13 of the project guidelines mandates comprehensive activity logging. To prevent security logs from becoming an unauthorized secondary repository of sensitive personal data, strict boundaries are enforced:

### 4.1 What IS Logged in `activity_logs`:
- High-level event categories (`REGISTERED`, `LOGIN_SUCCESS`, `LOGIN_FAILED`, `CAPTCHA_FAILED`, `LOCKOUT_TIER_1`, `PASSWORD_RESET_SUCCESS`, `REQUEST_SUBMITTED`).
- The username submitted in the request.
- Matched user ID (if account exists).
- Client IP address (`$_SERVER['REMOTE_ADDR']`).
- Client browser User-Agent string.
- High-level descriptive operational summary.
- Server timestamp (`created_at`).

### 4.2 What is STRICTLY EXCLUDED from Logs:
- Plaintext or hashed passwords.
- Plaintext or hashed security answers.
- Live 6-digit OTP codes.
- Credit/financial data.
- Detailed farm assistance request contents, farm locations, or support program details.
- AES encryption keys or IV secrets.

---

## 5. Farmer Consent Mechanism

The registration workflow implements an explicit, unbundled consent mechanism:
1. **Unchecked Checkbox:** The registration form (Step 5 — Security & Consent) contains an explicit checkbox (`privacy_consent`) that is unchecked by default. Form submission without checking this box is rejected on both client-side and server-side.
2. **Access to Policy:** An immediate hyperlink directs the farmer to `privacy_notice.php`, explaining data processing terms prior to registration.
3. **Audit Evidence:** Upon successful registration, the exact timestamp of consent is permanently written to `users.privacy_consent_at`, providing non-repudiable legal proof of informed consent under RA 10173.
