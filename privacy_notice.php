<?php
/**
 * Data Privacy Notice (RA 10173 - Philippine Data Privacy Act of 2012)
 * IT 107 - Information Assurance and Security 1
 * Implements Protection S8 & Handout Section 4.3
 * System: Secure Farmers Registration, Assistance and Monitoring System
 * Student ID: 2020-1050
 */

$pageTitle = 'Privacy Notice (RA 10173)';
require_once __DIR__ . '/php/header.php';
?>

<div class="card" style="max-width: 860px; margin: 20px auto; line-height: 1.8;">
    <h1 class="card-title" style="border-bottom: 2px solid var(--border); padding-bottom: 12px; margin-bottom: 18px;">
        &#128220; Data Privacy Notice &amp; Consent
    </h1>
    <p class="field-hint" style="margin-bottom: 20px;">
        In compliance with the <strong>Philippine Data Privacy Act of 2012 (Republic Act No. 10173)</strong>, its Implementing Rules and Regulations, and National Privacy Commission guidelines.
    </p>

    <section style="margin-bottom: 24px;">
        <h3 style="color: var(--primary-dark); font-size: 16px; margin-bottom: 8px;">1. Purpose of Data Processing</h3>
        <p>The <strong>Secure Farmers Registration, Assistance and Monitoring System</strong> collects and processes personal information for the exclusive purpose of evaluating, managing, and distributing agricultural subsidies, farm equipment support, and agronomy advisory services under the Department of Agriculture and Caraga State University research extensions.</p>
    </section>

    <section style="margin-bottom: 24px;">
        <h3 style="color: var(--primary-dark); font-size: 16px; margin-bottom: 8px;">2. Personal and Sensitive Data Collected</h3>
        <table class="data-table" style="margin-top: 10px;">
            <thead>
                <tr>
                    <th style="width: 28%;">Data Element</th>
                    <th>Legal Basis &amp; Protection Standard</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Full Name, Birthdate &amp; Age</strong></td>
                    <td>Required to establish unique farmer identity, verify legal adult consent (must be &ge; 18 years old), and link agricultural support records.</td>
                </tr>
                <tr>
                    <td><strong>Permanent Farm &amp; Residential Address</strong></td>
                    <td>Ensures accurate geolocation of farm parcels for crop assistance, soil sampling, and municipal subsidy eligibility.</td>
                </tr>
                <tr>
                    <td><strong>Contact Mobile Number</strong></td>
                    <td><strong>Sensitive Field:</strong> Encrypted at rest in the database using <strong>AES-256</strong> (Protection 13). Plaintext is never exposed in raw database storage. Decrypted only when displayed to the authenticated farmer or authorized agricultural officers.</td>
                </tr>
                <tr>
                    <td><strong>Farmer Classification &amp; Crop Commodity</strong></td>
                    <td>Used exclusively to allocate appropriate seed varieties, fertilizers, and agricultural equipment assistance.</td>
                </tr>
                <tr>
                    <td><strong>Authentication Audit Logs</strong></td>
                    <td>Maintained in an append-only security log to detect credential guessing, automated attacks, and protect registry integrity (Section 13.4).</td>
                </tr>
            </tbody>
        </table>
    </section>

    <section style="margin-bottom: 24px;">
        <h3 style="color: var(--primary-dark); font-size: 16px; margin-bottom: 8px;">3. Data Subject Rights (RA 10173 Section 16)</h3>
        <p>As a registered farmer, you are entitled to the following rights under RA 10173:</p>
        <ul style="margin-left: 24px; margin-top: 8px;">
            <li><strong>Right to be Informed:</strong> To know that your personal data is collected and processed.</li>
            <li><strong>Right to Access:</strong> To view your profile, contact details, and submitted farm assistance requests upon login.</li>
            <li><strong>Right to Rectification:</strong> To correct inaccurate information through official administrative channels.</li>
            <li><strong>Right to Erasure / Blocking:</strong> To request account deactivation subject to statutory retention requirements.</li>
            <li><strong>Right to Damages:</strong> To be indemnified for damages due to unlawful data processing.</li>
        </ul>
    </section>

    <section style="margin-bottom: 24px;">
        <h3 style="color: var(--primary-dark); font-size: 16px; margin-bottom: 8px;">4. Contact the Data Protection Officer</h3>
        <p>For inquiries regarding your rights under the Data Privacy Act (access, correction, or erasure), contact the Agricultural Data Officer at: <a href="mailto:jerime.tagalog@csucc.edu.ph">jerime.tagalog@csucc.edu.ph</a>.</p>
    </section>

    <div style="text-align: center; margin-top: 30px; border-top: 1px solid var(--border); padding-top: 18px;">
        <a href="register.php" class="btn btn-primary">&#8592; Return to Registration</a>
        <a href="index.php" class="btn btn-secondary" style="margin-left: 10px;">Return to Home</a>
    </div>
</div>

<?php require_once __DIR__ . '/php/footer.php'; ?>
