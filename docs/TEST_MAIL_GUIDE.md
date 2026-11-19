# EMAIL DISPATCH & SMTP TESTING GUIDE (test_mail.php)
**Course:** IT 107 – Information Assurance and Security 1  
**Project:** Secure Farmers Registration, Assistance and Monitoring System  
**Student:** Jerime Tagalog (`jerime.tagalog@csucc.edu.ph`)  
**Student ID:** 2020-1050  
**Security Focus:** Section 6 (Out-of-Band Email OTP) & Section 9 (Automated Security Alert Emails)

---

## 1. Overview & Purpose

In compliance with **Protection S8 (Two-Step Authentication & Out-of-Band Delivery)** and **Section 6** of the IT 107 Handout, all multi-factor authentication (MFA) codes, account activation OTPs, password reset verifications, and lockout notification alerts are transmitted out-of-band via encrypted SMTP using Google Mail's mail transport agents.

The **`test_mail.php`** script is a dedicated CLI and operational verification utility that allows instructors, evaluators, and system developers to instantly verify end-to-end SMTP dispatch without requiring a full manual registration or login cycle.

---

## 2. Pre-Requisites for Email Dispatch

Before running the test script, ensure the following configurations are met:

1. **Active Internet Connection:** Required to resolve `smtp.gmail.com` and connect to Google SMTP servers over TLS.
2. **Valid `config.php` Configuration:**
   - SMTP host: `smtp.gmail.com`
   - SMTP port: `587`
   - Encryption: `tls` (STARTTLS)
   - Account: `jerime.tagalog@csucc.edu.ph`
   - Password: 16-character Google App Password (not your ordinary account password)
3. **PHPMailer Dependencies:** Installed inside `vendor/` (bundled with project).

---

## 3. How to Execute the Email Test

### Method 1: Using Windows PowerShell or Terminal (Recommended)

1. Open **PowerShell** or **Command Prompt** on your computer.
2. Navigate to your project directory:
   ```powershell
   cd C:\xampp\htdocs\farmers_system
   ```
3. Run the script using the XAMPP PHP CLI binary:
   ```powershell
   & "C:\xampp\php\php.exe" test_mail.php
   ```

---

### Method 2: One-Liner Execution from Any Directory

If you are not currently in the project folder, run:
```powershell
& "C:\xampp\php\php.exe" "C:\xampp\htdocs\farmers_system\test_mail.php"
```

---

## 4. Expected Output & Demonstration Behavior

### Successful Dispatch (Standard Case)
When SMTP handshake and Google App Password authentication succeed, the terminal outputs:

```text
====================================================
Testing Live Email OTP Dispatch (Google SMTP)
Recipient: jerime.tagalog@csucc.edu.ph
Generated OTP: 483721
====================================================

[ SUCCESS ] Email sent successfully to jerime.tagalog@csucc.edu.ph!
Check your Gmail inbox (or Spam folder) for the 6-digit OTP code: 483721
```

### Verifying the Email Delivery in Inbox
1. Open your web browser and navigate to **[mail.google.com](https://mail.google.com)**.
2. Sign in with **`jerime.tagalog@csucc.edu.ph`**.
3. Check the inbox (or Spam/Junk folder if not yet whitelisted).
4. You will see an email with:
   - **Sender:** `Secure Farmers Registration, Assistance and Monitoring System <jerime.tagalog@csucc.edu.ph>`
   - **Subject:** `Your Verification Code - Farmers System`
   - **Body:** Styled HTML template containing the highlighted 6-digit numerical code (`483721`), notice of **5-minute validity window**, and confidentiality warning.

---

## 5. Security Protections Verified by this Test

| Handout Section | Protection Verified | Technical Implementation |
|:---|:---|:---|
| **Section 6.1** | Cryptographic Random Generation | 6-digit numeric OTP generated via `random_int(100000, 999999)` with cryptographically secure pseudo-randomness (CSPRNG). |
| **Section 6.2** | Out-of-Band Delivery | Transmission occurs outside the web HTTP channel via an independent encrypted network protocol (TLS 1.2+ over port 587). |
| **Section 6.3** | Confidentiality in Transit | Strict transport encryption ensures credentials cannot be intercepted by packet sniffing or man-in-the-middle (MITM) attacks. |
| **Protection 11** | Secrets Separation | SMTP passwords and API credentials are kept strictly in `config.php` and excluded by `.gitignore`. `test_mail.php` itself does not store hardcoded secrets. |

---

## 6. Common Issues & Troubleshooting

### Error: `[ FAILED ] Could not send email.`

#### Cause 1: Google App Password Mismatch or Expired
- **Symptom:** Terminal displays `SMTP Error: Could not authenticate.` or logs an authentication exception in `logs/error.log`.
- **Solution:**
  1. Visit [myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords).
  2. Generate a new 16-character App Password under your university Google account.
  3. Open `config.php` and update the `'password'` field inside the `'smtp'` block:
     ```php
     'smtp' => [
         'host'       => 'smtp.gmail.com',
         'port'       => 587,
         'username'   => 'jerime.tagalog@csucc.edu.ph',
         'password'   => 'your-16-char-password-here', // No spaces
         'secure'     => 'tls',
         'auth'       => true,
         // ...
     ]
     ```

#### Cause 2: Blocked Port 587 or Firewall Restrictions
- **Symptom:** Connection timed out or connection refused.
- **Solution:** Ensure your local antivirus, firewall, or university Wi-Fi network does not block outbound connections to `smtp.gmail.com:587`. You can test connection reachability using PowerShell:
  ```powershell
  Test-NetConnection -ComputerName smtp.gmail.com -Port 587
  ```
  The result should show `TcpTestSucceeded : True`.

#### Cause 3: 2-Step Verification Disabled on Google Account
- **Symptom:** Google prevents App Password generation.
- **Solution:** Turn on 2-Step Verification on `jerime.tagalog@csucc.edu.ph` before creating App Passwords.

---

## 7. Operational Cleanliness & Security Note

> [!IMPORTANT]
> The `test_mail.php` script is listed in **`.gitignore`** so that local testing credentials and triggers are never exposed on public Git repositories. It is strictly a local developer testing and defense utility.
