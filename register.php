<?php
/**
 * Farmer & Producer Registration Form
 * IT 107 - Information Assurance and Security 1
 * Implements Handout Section 4, 5, 6, 7 and Protections 1, 2, 3, 8, 12, 13
 * Multistep Wizard (Next / Previous without vertical scroll fatigue)
 */

$pageTitle = 'Farmer Registration';
$extraJs = 'js/strength.js';
require_once __DIR__ . '/php/header.php';
require_once __DIR__ . '/php/db.php';
require_once __DIR__ . '/php/security.php';
require_once __DIR__ . '/php/validation.php';
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/captcha.php';
require_once __DIR__ . '/php/logger.php';

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$values = [
    'id_number' => '',
    'first_name' => '',
    'middle_name' => '',
    'family_name' => '',
    'name_extension_select' => '',
    'name_extension_other' => '',
    'birthdate' => '',
    'age' => '',
    'purok_street' => '',
    'barangay' => '',
    'municipality_city' => '',
    'province' => '',
    'country' => 'Philippines',
    'zip_code' => '',
    'email' => '',
    'username' => '',
    'contact_number' => '',
    'farmer_type' => '',
    'primary_crop' => '',
    'emergency_contact' => '',
    'sel_q1' => 'Q2',
    'sel_q2' => 'Q5',
    'sel_q3' => 'Q7',
    'ans_1' => '',
    'ans_2' => '',
    'ans_3' => '',
    'ans_q2' => '',
    'ans_q5' => '',
    'ans_q7' => '',
    'privacy_consent' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Protection 8: Honeypot check
    if (!empty($_POST['farm_partner_website'])) {
        // Bot detected: Silent rejection
        log_activity('BOT_DETECTED', null, null, 'Honeypot field was filled during registration.');
        header('Location: index.php');
        exit;
    }

    // 2. Protection 3: CSRF token verification
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        log_activity('CSRF_REJECTED', null, null, 'Invalid CSRF token during registration submission.');
        $errors['form'] = 'Session security token invalid or expired. Please reload the page.';
    }

    // 3. Protection S7: CAPTCHA verification
    if (!verify_captcha($_POST['captcha_code'] ?? '')) {
        log_activity('CAPTCHA_FAILED', null, null, 'Registration CAPTCHA failed.');
        $errors['captcha_code'] = 'Incorrect security code. Please try again.';
    }

    // Read and sanitize raw inputs
    foreach ($values as $key => $default) {
        if ($key === 'privacy_consent') {
            $values[$key] = isset($_POST[$key]) ? '1' : '';
        } else {
            $values[$key] = trim($_POST[$key] ?? '');
        }
    }

    // 4. Validate Core Identity Fields (Handout 4.4, 4.5, 4.6, 4.7, 4.8, 4.9)
    $err = validate_id_number($values['id_number']);
    if ($err) $errors['id_number'] = $err;

    $err = validate_name_field($values['first_name'], 'First Name', true, 50);
    if ($err) $errors['first_name'] = $err;

    if ($values['middle_name'] !== '') {
        $err = validate_name_field($values['middle_name'], 'Middle Name', false, 50);
        if ($err) $errors['middle_name'] = $err;
    }

    $err = validate_name_field($values['family_name'], 'Family Name', true, 50);
    if ($err) $errors['family_name'] = $err;

    $finalExtension = $values['name_extension_select'];
    if ($finalExtension === 'Other') {
        $finalExtension = $values['name_extension_other'];
        $err = validate_name_field($finalExtension, 'Name Extension', false, 15);
        if ($err) $errors['name_extension'] = $err;
    }

    $err = validate_birthdate_and_age($values['birthdate'], $values['age']);
    if ($err) $errors['birthdate'] = $err;

    // 5. Validate Address Fields (Section 4.7)
    $err = validate_address_field($values['purok_street'], 'Purok / Street', true, 50);
    if ($err) $errors['purok_street'] = $err;

    $err = validate_address_field($values['barangay'], 'Barangay', true, 50);
    if ($err) $errors['barangay'] = $err;

    $err = validate_address_field($values['municipality_city'], 'Municipality / City', true, 50);
    if ($err) $errors['municipality_city'] = $err;

    $err = validate_address_field($values['province'], 'Province', true, 50);
    if ($err) $errors['province'] = $err;

    $err = validate_address_field($values['country'], 'Country', true, 50);
    if ($err) $errors['country'] = $err;

    $err = validate_zip_code($values['zip_code']);
    if ($err) $errors['zip_code'] = $err;

    // 6. Contact Number & Farmer Info
    $err = validate_contact_number($values['contact_number']);
    if ($err) $errors['contact_number'] = $err;

    if ($values['emergency_contact'] !== '') {
        $err = validate_contact_number($values['emergency_contact']);
        if ($err) $errors['emergency_contact'] = $err;
    }

    $err = validate_email($values['email']);
    if ($err) $errors['email'] = $err;

    if (empty($values['farmer_type'])) {
        $errors['farmer_type'] = 'Please select a farmer classification.';
    }

    if (empty($values['primary_crop'])) {
        $errors['primary_crop'] = 'Please select a primary agricultural crop.';
    }

    // 7. Username & Password Validation
    $err = validate_username($values['username']);
    if ($err) $errors['username'] = $err;

    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $err = validate_password_complexity($password);
    if ($err) $errors['password'] = $err;

    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // 8. Security Questions Validation (Section 5)
    $err = validate_security_question_set(
        $values['sel_q1'], $values['ans_1'] !== '' ? $values['ans_1'] : $values['ans_q2'],
        $values['sel_q2'], $values['ans_2'] !== '' ? $values['ans_2'] : $values['ans_q5'],
        $values['sel_q3'], $values['ans_3'] !== '' ? $values['ans_3'] : $values['ans_q7']
    );
    if ($err) $errors['security_questions'] = $err;

    // 9. Privacy Consent Validation (Section 4.3)
    if (empty($values['privacy_consent'])) {
        $errors['privacy_consent'] = 'You must agree to the Privacy Notice in accordance with RA 10173.';
    }

    // Check duplicate ID number, Email, Username in Database
    if (empty($errors)) {
        $pdo = get_db_connection();

        $dupStmt = $pdo->prepare("SELECT id_number, username, email FROM users WHERE id_number = :id OR username = :u OR email = :e LIMIT 1");
        $dupStmt->execute([
            ':id' => $values['id_number'],
            ':u'  => $values['username'],
            ':e'  => strtolower($values['email'])
        ]);
        $existing = $dupStmt->fetch();

        if ($existing) {
            if ($existing['id_number'] === $values['id_number']) {
                $errors['id_number'] = 'This ID Number is already registered.';
            }
            if (strtolower($existing['username']) === strtolower($values['username'])) {
                $errors['username'] = 'This username is already taken.';
            }
            if (strtolower($existing['email']) === strtolower($values['email'])) {
                $errors['email'] = 'An account with this email address already exists.';
            }
        }
    }

    // 10. Persist Farmer Account & Hash Sensitive Data
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // S1: Password Hashing (Bcrypt)
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Protection 13: AES-256 field-level encryption for contact numbers
            $contactEncrypted = encrypt_field($values['contact_number']);
            $emergencyEncrypted = !empty($values['emergency_contact']) ? encrypt_field($values['emergency_contact']) : null;

            // Compute server-calculated age
            $bdateObj = new DateTime($values['birthdate']);
            $today = new DateTime();
            $calculatedAge = $today->diff($bdateObj)->y;

            $now = date('Y-m-d H:i:s');

            // Insert into users
            $userStmt = $pdo->prepare("
                INSERT INTO `users` (
                    `id_number`, `first_name`, `middle_name`, `family_name`, `name_extension`,
                    `birthdate`, `age`, `purok_street`, `barangay`, `municipality_city`,
                    `province`, `country`, `zip_code`, `email`, `username`, `password_hash`,
                    `contact_number_encrypted`, `farmer_type`, `primary_crop`,
                    `emergency_contact_encrypted`, `role`, `is_verified`, `privacy_consent_at`,
                    `created_at`, `updated_at`
                ) VALUES (
                    :id, :first, :middle, :family, :ext,
                    :bdate, :age, :purok, :brgy, :city,
                    :province, :country, :zip, :email, :username, :pwd_hash,
                    :contact_enc, :farmer_type, :primary_crop,
                    :emergency_enc, 'farmer', 0, :consent_at,
                    :created, :updated
                )
            ");

            $userStmt->execute([
                ':id'             => $values['id_number'],
                ':first'          => $values['first_name'],
                ':middle'         => $values['middle_name'] !== '' ? $values['middle_name'] : null,
                ':family'         => $values['family_name'],
                ':ext'            => $finalExtension,
                ':bdate'          => $values['birthdate'],
                ':age'            => $calculatedAge,
                ':purok'          => $values['purok_street'],
                ':brgy'           => $values['barangay'],
                ':city'           => $values['municipality_city'],
                ':province'       => $values['province'],
                ':country'        => $values['country'],
                ':zip'            => $values['zip_code'],
                ':email'          => strtolower($values['email']),
                ':username'       => $values['username'],
                ':pwd_hash'       => $passwordHash,
                ':contact_enc'    => $contactEncrypted,
                ':farmer_type'    => $values['farmer_type'],
                ':primary_crop'   => $values['primary_crop'],
                ':emergency_enc'  => $emergencyEncrypted,
                ':consent_at'     => $now,
                ':created'        => $now,
                ':updated'        => $now
            ]);

            // Insert normalized security answers (lowercase, trimmed, hashed) (Section 5)
            $authStmt = $pdo->prepare("
                INSERT INTO `authentication_answers` (`user_id`, `question_key`, `answer_hash`, `created_at`)
                VALUES (:uid, :qkey, :ahash, :created)
            ");

            $questions = [
                $values['sel_q1'] => strtolower($values['ans_1']),
                $values['sel_q2'] => strtolower($values['ans_2']),
                $values['sel_q3'] => strtolower($values['ans_3'])
            ];

            foreach ($questions as $qKey => $cleanAns) {
                $authStmt->execute([
                    ':uid'     => $values['id_number'],
                    ':qkey'    => $qKey,
                    ':ahash'   => password_hash($cleanAns, PASSWORD_DEFAULT),
                    ':created' => $now
                ]);
            }

            // Record initial password in password history (Protection 7)
            add_password_to_history($values['id_number'], $passwordHash);

            // Append-only activity log (Section 13.4)
            log_activity('REGISTERED', $values['username'], $values['id_number'], 'New farmer account registered. Awaiting email OTP verification.');

            $pdo->commit();

            // Dispatch 6-digit Registration OTP (Section 6)
            $fullName = $values['first_name'] . ' ' . $values['family_name'];
            create_otp_for_user($values['id_number'], $values['email'], $fullName, 'register');

            // Store pending OTP session variables
            $_SESSION['pending_otp_user_id'] = $values['id_number'];
            $_SESSION['pending_otp_purpose'] = 'register';
            $_SESSION['pending_otp_email'] = $values['email'];

            header('Location: verify_otp.php');
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('[REGISTRATION ERROR] ' . $e->getMessage(), 3, dirname(__DIR__) . '/logs/error.log');
            $errors['form'] = 'A registration database error occurred. Please try again.';
        }
    }
}
?>

<style>
/* Multistep Stepper Navigation */
.stepper-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin: 10px 0 28px 0;
    padding: 0 10px;
}

.stepper-container::before {
    content: "";
    position: absolute;
    top: 20px;
    left: 40px;
    right: 40px;
    height: 3px;
    background: #e5e7eb;
    z-index: 1;
}

.step-indicator {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    flex: 1;
    cursor: default;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid #d1d5db;
    color: #6b7280;
    font-weight: 700;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.25s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.step-indicator.active .step-circle {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    border-color: #b45309;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.2);
}

.step-indicator.completed .step-circle {
    background: #059669;
    border-color: #047857;
    color: #ffffff;
}

.step-label {
    font-size: 11.5px;
    font-weight: 700;
    color: #6b7280;
    margin-top: 8px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    max-width: 90px;
    line-height: 1.3;
}

.step-indicator.active .step-label {
    color: var(--primary-dark);
}

.step-indicator.completed .step-label {
    color: #059669;
}

/* Wizard Page Panels */
.wizard-step-pane {
    display: none;
    animation: fadeInWizard 0.25s ease-in-out;
}

.wizard-step-pane.active {
    display: block;
}

@keyframes fadeInWizard {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.wizard-nav-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 32px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
}

.step-header-box {
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
}

.step-header-title {
    color: var(--primary-dark);
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 4px 0;
    font-family: 'Outfit', sans-serif;
    display: flex;
    align-items: center;
    gap: 8px;
}

.step-header-desc {
    font-size: 13px;
    color: var(--text-muted);
    margin: 0;
}

@media (max-width: 600px) {
    .step-label {
        display: none;
    }
    .stepper-container {
        margin-bottom: 18px;
    }
    .stepper-container::before {
        top: 20px;
    }
}
</style>

<div class="card" style="max-width: 860px; margin: 0 auto;">
    <h1 class="card-title">Farmer Account Registration</h1>
    <p class="card-subtitle">Complete the steps below to register your agricultural profile. Information is validated at each step.</p>

    <!-- Stepper Progress Bar (Step 1 to 5) -->
    <div class="stepper-container" id="stepper-nav">
        <div class="step-indicator active" id="step-ind-1">
            <div class="step-circle">1</div>
            <div class="step-label">Personal</div>
        </div>
        <div class="step-indicator" id="step-ind-2">
            <div class="step-circle">2</div>
            <div class="step-label">Address</div>
        </div>
        <div class="step-indicator" id="step-ind-3">
            <div class="step-circle">3</div>
            <div class="step-label">Farm &amp; Contact</div>
        </div>
        <div class="step-indicator" id="step-ind-4">
            <div class="step-circle">4</div>
            <div class="step-label">Credentials</div>
        </div>
        <div class="step-indicator" id="step-ind-5">
            <div class="step-circle">5</div>
            <div class="step-label">Security &amp; Consent</div>
        </div>
    </div>

    <?php if (!empty($errors['form'])): ?>
        <div class="alert alert-danger">&#9888; <?= e($errors['form']) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php" id="registration-form" novalidate>
        <!-- Protection 3: CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">

        <!-- Protection 8: Honeypot field (hidden from genuine users via display:none) -->
        <div style="display:none !important;" aria-hidden="true">
            <label>Do not fill this field if human:</label>
            <input type="text" name="farm_partner_website" tabindex="-1" autocomplete="off">
        </div>

        <!-- ===============================================================
             STEP 1: PERSONAL & IDENTIFICATION
             =============================================================== -->
        <div class="wizard-step-pane active" id="wizard-step-1">
            <div class="step-header-box">
                <h3 class="step-header-title"><span>&#128100;</span> Step 1: Farmer Identification &amp; Personal Info</h3>
                <p class="step-header-desc">Enter your university/government issued ID number and official personal details.</p>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="id_number" class="form-label">ID Number<span class="required-star">*</span></label>
                    <input type="text" id="id_number" name="id_number" class="form-control <?= isset($errors['id_number']) ? 'is-invalid' : '' ?>"
                           placeholder="xxxx-xxxx" maxlength="9" value="<?= e($values['id_number']) ?>" required>
                    <span id="error-id_number" class="field-error"><?= e($errors['id_number'] ?? '') ?></span>
                    <span class="field-hint">Format: 2020-1050 (Assigned University ID)</span>
                </div>

                <div class="form-group">
                    <label for="farmer_type" class="form-label">Farmer Classification<span class="required-star">*</span></label>
                    <select id="farmer_type" name="farmer_type" class="form-control <?= isset($errors['farmer_type']) ? 'is-invalid' : '' ?>" required>
                        <option value="">-- Select Farmer Type --</option>
                        <option value="Smallholder Farmer" <?= $values['farmer_type'] === 'Smallholder Farmer' ? 'selected' : '' ?>>Smallholder Farmer</option>
                        <option value="Tenant Farmer" <?= $values['farmer_type'] === 'Tenant Farmer' ? 'selected' : '' ?>>Tenant Farmer</option>
                        <option value="Commercial Farmer" <?= $values['farmer_type'] === 'Commercial Farmer' ? 'selected' : '' ?>>Commercial Farmer</option>
                        <option value="Agricultural Worker" <?= $values['farmer_type'] === 'Agricultural Worker' ? 'selected' : '' ?>>Agricultural Worker</option>
                    </select>
                    <span id="error-farmer_type" class="field-error"><?= e($errors['farmer_type'] ?? '') ?></span>
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name<span class="required-star">*</span></label>
                    <input type="text" id="first_name" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                           maxlength="21" value="<?= e($values['first_name']) ?>" placeholder="e.g. Juan" required>
                    <span id="error-first_name" class="field-error"><?= e($errors['first_name'] ?? '') ?></span>
                </div>

                <div class="form-group">
                    <label for="middle_name" class="form-label">Middle Name<span class="optional-text">optional</span></label>
                    <input type="text" id="middle_name" name="middle_name" class="form-control <?= isset($errors['middle_name']) ? 'is-invalid' : '' ?>"
                           maxlength="21" value="<?= e($values['middle_name']) ?>" placeholder="e.g. Santos">
                    <span id="error-middle_name" class="field-error"><?= e($errors['middle_name'] ?? '') ?></span>
                </div>

                <div class="form-group">
                    <label for="family_name" class="form-label">Family Name<span class="required-star">*</span></label>
                    <input type="text" id="family_name" name="family_name" class="form-control <?= isset($errors['family_name']) ? 'is-invalid' : '' ?>"
                           maxlength="21" value="<?= e($values['family_name']) ?>" placeholder="e.g. Dela Cruz" required>
                    <span id="error-family_name" class="field-error"><?= e($errors['family_name'] ?? '') ?></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="name_extension_select" class="form-label">Name Extension<span class="optional-text">optional</span></label>
                    <select id="name_extension_select" name="name_extension_select" class="form-control">
                        <option value="">-- None --</option>
                        <option value="Jr." <?= $values['name_extension_select'] === 'Jr.' ? 'selected' : '' ?>>Jr.</option>
                        <option value="Sr." <?= $values['name_extension_select'] === 'Sr.' ? 'selected' : '' ?>>Sr.</option>
                        <option value="Other" <?= $values['name_extension_select'] === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                    <input type="text" id="name_extension_other" name="name_extension_other" class="form-control"
                           style="margin-top: 8px; display: <?= $values['name_extension_select'] === 'Other' ? 'block' : 'none' ?>;"
                           placeholder="Type extension (e.g. III, IV, X)" value="<?= e($values['name_extension_other']) ?>">
                    <span id="error-name_extension" class="field-error"><?= e($errors['name_extension'] ?? '') ?></span>
                </div>

                <div class="form-group">
                    <label for="birthdate" class="form-label">Birthdate<span class="required-star">*</span></label>
                    <input type="date" id="birthdate" name="birthdate" class="form-control <?= isset($errors['birthdate']) ? 'is-invalid' : '' ?>"
                           value="<?= e($values['birthdate']) ?>" required>
                    <span id="error-birthdate" class="field-error"><?= e($errors['birthdate'] ?? '') ?></span>
                </div>

                <div class="form-group">
                    <label for="age" class="form-label">Age (Auto-calculated)<span class="required-star">*</span></label>
                    <input type="number" id="age" name="age" class="form-control" value="<?= e($values['age']) ?>" readonly placeholder="Auto">
                    <span class="field-hint">Recalculated on server. Must be &ge; 18.</span>
                </div>
            </div>
        </div>

        <!-- ===============================================================
             STEP 2: PERMANENT RESIDENTIAL ADDRESS
             =============================================================== -->
        <div class="wizard-step-pane" id="wizard-step-2">
            <div class="step-header-box">
                <h3 class="step-header-title"><span>&#127968;</span> Step 2: Permanent Residence Address</h3>
                <p class="step-header-desc">Provide your residential and farm jurisdiction address within the region.</p>
            </div>

            <div class="form-row">
                <div class="form-group" style="grid-column: span 2;">
                    <label for="purok_street" class="form-label">Purok / Street<span class="required-star">*</span></label>
                    <input type="text" id="purok_street" name="purok_street" class="form-control <?= isset($errors['purok_street']) ? 'is-invalid' : '' ?>"
                           maxlength="50" value="<?= e($values['purok_street']) ?>" placeholder="e.g. Purok 4 Ampayon" required>
                    <span id="error-purok_street" class="field-error"><?= e($errors['purok_street'] ?? '') ?></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="barangay" class="form-label">Barangay<span class="required-star">*</span></label>
                    <input type="text" id="barangay" name="barangay" class="form-control <?= isset($errors['barangay']) ? 'is-invalid' : '' ?>"
                           maxlength="50" value="<?= e($values['barangay']) ?>" placeholder="e.g. Ampayon" required>
                    <span id="error-barangay" class="field-error"><?= e($errors['barangay'] ?? '') ?></span>
                </div>

                <div class="form-group">
                    <label for="municipality_city" class="form-label">Municipality / City<span class="required-star">*</span></label>
                    <input type="text" id="municipality_city" name="municipality_city" class="form-control <?= isset($errors['municipality_city']) ? 'is-invalid' : '' ?>"
                           maxlength="50" value="<?= e($values['municipality_city']) ?>" placeholder="e.g. Butuan City" required>
                    <span id="error-municipality_city" class="field-error"><?= e($errors['municipality_city'] ?? '') ?></span>
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label for="province" class="form-label">Province<span class="required-star">*</span></label>
                    <input type="text" id="province" name="province" class="form-control <?= isset($errors['province']) ? 'is-invalid' : '' ?>"
                           maxlength="50" value="<?= e($values['province']) ?>" placeholder="e.g. Agusan Del Norte" required>
                    <span id="error-province" class="field-error"><?= e($errors['province'] ?? '') ?></span>
                </div>

                <div class="form-group">
                    <label for="country" class="form-label">Country<span class="required-star">*</span></label>
                    <input type="text" id="country" name="country" class="form-control <?= isset($errors['country']) ? 'is-invalid' : '' ?>"
                           value="<?= e($values['country']) ?>" required>
                    <span id="error-country" class="field-error"><?= e($errors['country'] ?? '') ?></span>
                </div>

                <div class="form-group">
                    <label for="zip_code" class="form-label">ZIP Code<span class="required-star">*</span></label>
                    <input type="text" id="zip_code" name="zip_code" class="form-control <?= isset($errors['zip_code']) ? 'is-invalid' : '' ?>"
                           maxlength="4" value="<?= e($values['zip_code']) ?>" placeholder="e.g. 8600" required>
                    <span id="error-zip_code" class="field-error"><?= e($errors['zip_code'] ?? '') ?></span>
                </div>
            </div>
        </div>

        <!-- ===============================================================
             STEP 3: CONTACT & FARM ENTERPRISE
             =============================================================== -->
        <div class="wizard-step-pane" id="wizard-step-3">
            <div class="step-header-box">
                <h3 class="step-header-title"><span>&#127806;</span> Step 3: Contact &amp; Agricultural Enterprise</h3>
                <p class="step-header-desc">Contact info is protected with AES-256 encryption. OTP verification will be sent to your email.</p>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contact_number" class="form-label">Contact Number<span class="required-star">*</span></label>
                    <input type="text" id="contact_number" name="contact_number" class="form-control <?= isset($errors['contact_number']) ? 'is-invalid' : '' ?>"
                           maxlength="11" value="<?= e($values['contact_number']) ?>" placeholder="09XXXXXXXXX" required>
                    <span id="error-contact_number" class="field-error"><?= e($errors['contact_number'] ?? '') ?></span>
                    <span class="field-hint">&#128274; Encrypted at rest with AES-256-CBC (Protection 13).</span>
                </div>

                <div class="form-group">
                    <label for="emergency_contact" class="form-label">Emergency Contact Number<span class="optional-text">optional</span></label>
                    <input type="text" id="emergency_contact" name="emergency_contact" class="form-control <?= isset($errors['emergency_contact']) ? 'is-invalid' : '' ?>"
                           maxlength="11" value="<?= e($values['emergency_contact']) ?>" placeholder="09XXXXXXXXX">
                    <span id="error-emergency_contact" class="field-error"><?= e($errors['emergency_contact'] ?? '') ?></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address<span class="required-star">*</span></label>
                    <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                           maxlength="100" value="<?= e($values['email']) ?>" placeholder="e.g. juan@example.com" required>
                    <span id="error-email" class="field-error"><?= e($errors['email'] ?? '') ?></span>
                    <span class="field-hint">Must be valid and accessible. Email OTP verification required.</span>
                </div>

                <div class="form-group">
                    <label for="primary_crop" class="form-label">Primary Crop / Commodity<span class="required-star">*</span></label>
                    <select id="primary_crop" name="primary_crop" class="form-control <?= isset($errors['primary_crop']) ? 'is-invalid' : '' ?>" required>
                        <option value="">-- Select Primary Crop --</option>
                        <option value="Rice" <?= $values['primary_crop'] === 'Rice' ? 'selected' : '' ?>>Rice (Palay)</option>
                        <option value="Corn" <?= $values['primary_crop'] === 'Corn' ? 'selected' : '' ?>>Corn (Mais)</option>
                        <option value="Coconut" <?= $values['primary_crop'] === 'Coconut' ? 'selected' : '' ?>>Coconut (Niyog)</option>
                        <option value="Banana" <?= $values['primary_crop'] === 'Banana' ? 'selected' : '' ?>>Banana</option>
                        <option value="Vegetables" <?= $values['primary_crop'] === 'Vegetables' ? 'selected' : '' ?>>Highland / Lowland Vegetables</option>
                        <option value="High-Value Crops" <?= $values['primary_crop'] === 'High-Value Crops' ? 'selected' : '' ?>>High-Value Crops (Cacao, Coffee, Fruits)</option>
                    </select>
                    <span id="error-primary_crop" class="field-error"><?= e($errors['primary_crop'] ?? '') ?></span>
                </div>
            </div>
        </div>

        <!-- ===============================================================
             STEP 4: ACCOUNT CREDENTIALS
             =============================================================== -->
        <div class="wizard-step-pane" id="wizard-step-4">
            <div class="step-header-box">
                <h3 class="step-header-title"><span>&#128274;</span> Step 4: Account Credentials &amp; Password</h3>
                <p class="step-header-desc">Set your username and strong master password.</p>
            </div>

            <div class="form-group">
                <label for="username" class="form-label">Username<span class="required-star">*</span></label>
                <input type="text" id="username" name="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                       maxlength="17" value="<?= e($values['username']) ?>" placeholder="6 to 17 letters & numbers" required>
                <span id="error-username" class="field-error"><?= e($errors['username'] ?? '') ?></span>
                <span class="field-hint">Must be 6 to 17 characters, start with a letter, letters &amp; numbers only. Checked live.</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">Password<span class="required-star">*</span></label>
                    <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                           maxlength="64" placeholder="Minimum 9 characters" required>
                    <!-- Live strength meter -->
                    <div class="strength-meter-container">
                        <div class="strength-bar-track">
                            <div id="strength-bar" class="strength-bar"></div>
                        </div>
                        <span id="strength-text" class="strength-text">None</span>
                    </div>
                    <span id="error-password" class="field-error"><?= e($errors['password'] ?? '') ?></span>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Re-enter Password<span class="required-star">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                           maxlength="64" placeholder="Repeat your password" required>
                    <span id="error-confirm_password" class="field-error"><?= e($errors['confirm_password'] ?? '') ?></span>
                </div>
            </div>
        </div>

        <!-- ===============================================================
             STEP 5: SECURITY QUESTIONS & PRIVACY CONSENT
             =============================================================== -->
        <div class="wizard-step-pane" id="wizard-step-5">
            <div class="step-header-box">
                <h3 class="step-header-title"><span>&#128737;</span> Step 5: Security Questions &amp; Final Consent</h3>
                <p class="step-header-desc">Select 3 different recovery questions and confirm data privacy compliance.</p>
            </div>

            <?php if (!empty($errors['security_questions'])): ?>
                <div class="alert alert-danger" style="margin-bottom: 15px; font-size: 13.5px;">
                    <?= $errors['security_questions'] ?>
                </div>
            <?php endif; ?>

            <!-- Question 1 -->
            <div class="form-group" style="background: var(--bg-page); border: 1px solid var(--border); border-radius: 8px; padding: 14px; margin-bottom: 14px;">
                <label for="sel_q1" class="form-label" style="font-weight: 700;">Security Question 1<span class="required-star">*</span></label>
                <select id="sel_q1" name="sel_q1" class="form-control" style="margin-bottom: 8px;" required>
                    <?php foreach (SECURITY_QUESTION_BANK as $qCode => $qText): ?>
                        <option value="<?= $qCode ?>" <?= $values['sel_q1'] === $qCode ? 'selected' : '' ?>>
                            <?= $qCode ?>: <?= e($qText) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" id="ans_1" name="ans_1" class="form-control"
                       maxlength="50" value="<?= e($values['ans_1'] !== '' ? $values['ans_1'] : $values['ans_q2']) ?>" placeholder="Answer to Question 1" required>
            </div>

            <!-- Question 2 -->
            <div class="form-group" style="background: var(--bg-page); border: 1px solid var(--border); border-radius: 8px; padding: 14px; margin-bottom: 14px;">
                <label for="sel_q2" class="form-label" style="font-weight: 700;">Security Question 2<span class="required-star">*</span></label>
                <select id="sel_q2" name="sel_q2" class="form-control" style="margin-bottom: 8px;" required>
                    <?php foreach (SECURITY_QUESTION_BANK as $qCode => $qText): ?>
                        <option value="<?= $qCode ?>" <?= $values['sel_q2'] === $qCode ? 'selected' : '' ?>>
                            <?= $qCode ?>: <?= e($qText) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" id="ans_2" name="ans_2" class="form-control"
                       maxlength="50" value="<?= e($values['ans_2'] !== '' ? $values['ans_2'] : $values['ans_q5']) ?>" placeholder="Answer to Question 2" required>
            </div>

            <!-- Question 3 -->
            <div class="form-group" style="background: var(--bg-page); border: 1px solid var(--border); border-radius: 8px; padding: 14px; margin-bottom: 14px;">
                <label for="sel_q3" class="form-label" style="font-weight: 700;">Security Question 3<span class="required-star">*</span></label>
                <select id="sel_q3" name="sel_q3" class="form-control" style="margin-bottom: 8px;" required>
                    <?php foreach (SECURITY_QUESTION_BANK as $qCode => $qText): ?>
                        <option value="<?= $qCode ?>" <?= $values['sel_q3'] === $qCode ? 'selected' : '' ?>>
                            <?= $qCode ?>: <?= e($qText) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" id="ans_3" name="ans_3" class="form-control"
                       maxlength="50" value="<?= e($values['ans_3'] !== '' ? $values['ans_3'] : $values['ans_q7']) ?>" placeholder="Answer to Question 3" required>
            </div>

            <!-- Privacy Notice Checkbox -->
            <div class="form-group" style="background-color: var(--primary-subtle); padding: 16px; border: 1px solid var(--border); border-radius: 8px; margin-top: 18px;">
                <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; font-size: 13.5px; line-height: 1.4;">
                    <input type="checkbox" id="privacy_consent" name="privacy_consent" value="1"
                           <?= $values['privacy_consent'] === '1' ? 'checked' : '' ?> style="margin-top: 3px;" required>
                    <span>
                        I have read the <a href="privacy_notice.php" target="_blank" style="color: var(--primary-dark); font-weight: 700; text-decoration: underline;">Privacy Notice</a>
                        and agree to the collection and processing of my personal data in accordance with the Data Privacy Act of 2012 (RA 10173).<span class="required-star">*</span>
                    </span>
                </label>
                <span id="error-privacy_consent" class="field-error"><?= e($errors['privacy_consent'] ?? '') ?></span>
            </div>

            <!-- CAPTCHA Two-Row Layout -->
            <div class="form-group" style="margin-top: 20px;">
                <label for="captcha_code" class="form-label">Security Verification (CAPTCHA)<span class="required-star">*</span></label>
                <!-- Row 1: Image & Refresh Button -->
                <div style="display: flex; align-items: center; justify-content: space-between; background: #faf7f2; border: 1.5px solid #cbbfb0; border-radius: var(--radius-md); padding: 8px 12px; margin-bottom: 10px;">
                    <img src="php/captcha_image.php?t=<?= time() ?>" id="captcha-img" alt="CAPTCHA" style="height: 44px; border-radius: 6px; border: 1px solid #d6cbbe; background: #ffffff;">
                    <button type="button" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; font-weight: 700;" onclick="document.getElementById('captcha-img').src='php/captcha_image.php?t='+new Date().getTime()">
                        <span>&#8634;</span> Refresh Code
                    </button>
                </div>
                <!-- Row 2: Full-width Input Field -->
                <input type="text" id="captcha_code" name="captcha_code" class="form-control"
                       placeholder="Enter the security code shown above" maxlength="6" required>
                <span id="error-captcha_code" class="field-error"><?= e($errors['captcha_code'] ?? '') ?></span>
                <span class="field-hint">Case-insensitive. Single-use bot protection.</span>
            </div>
        </div>

        <!-- ===============================================================
             WIZARD ACTION BUTTONS (Previous / Next / Submit)
             =============================================================== -->
        <div class="wizard-nav-actions">
            <div>
                <button type="button" id="prev-step-btn" class="btn btn-secondary" style="display: none; padding: 11px 24px;" onclick="goToPreviousStep()">
                    &#8592; Previous Step
                </button>
            </div>
            <div>
                <button type="button" id="next-step-btn" class="btn btn-primary" style="padding: 11px 28px;" onclick="goToNextStep()">
                    Next Step &#8594;
                </button>
                <button type="submit" id="submit-btn" class="btn btn-primary" style="display: none; padding: 12px 34px; font-size: 15px;">
                    Complete Registration &amp; Verify Email &#8594;
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Wizard State Controller
var currentStep = 1;
var totalSteps = 5;

// If server returned errors on specific fields, open that step directly
<?php
$errorStep = 1;
if (isset($errors['purok_street']) || isset($errors['barangay']) || isset($errors['municipality_city']) || isset($errors['province']) || isset($errors['country']) || isset($errors['zip_code'])) {
    $errorStep = 2;
} elseif (isset($errors['contact_number']) || isset($errors['emergency_contact']) || isset($errors['email']) || isset($errors['primary_crop'])) {
    $errorStep = 3;
} elseif (isset($errors['username']) || isset($errors['password']) || isset($errors['confirm_password'])) {
    $errorStep = 4;
} elseif (isset($errors['security_questions']) || isset($errors['privacy_consent']) || isset($errors['captcha_code'])) {
    $errorStep = 5;
}
?>
var initialServerStep = <?= $errorStep ?>;

function updateWizardUI() {
    // Show active pane, hide others
    for (var i = 1; i <= totalSteps; i++) {
        var pane = document.getElementById('wizard-step-' + i);
        var ind = document.getElementById('step-ind-' + i);
        if (pane) {
            if (i === currentStep) {
                pane.classList.add('active');
            } else {
                pane.classList.remove('active');
            }
        }
        if (ind) {
            ind.classList.remove('active', 'completed');
            if (i < currentStep) {
                ind.classList.add('completed');
            } else if (i === currentStep) {
                ind.classList.add('active');
            }
        }
    }

    var prevBtn = document.getElementById('prev-step-btn');
    var nextBtn = document.getElementById('next-step-btn');
    var submitBtn = document.getElementById('submit-btn');

    if (currentStep === 1) {
        prevBtn.style.display = 'none';
    } else {
        prevBtn.style.display = 'inline-flex';
    }

    if (currentStep === totalSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-flex';
    } else {
        nextBtn.style.display = 'inline-flex';
        submitBtn.style.display = 'none';
    }

    // Smooth scroll card to top
    window.scrollTo({ top: 120, behavior: 'smooth' });
}

function validateCurrentStep(step) {
    var pane = document.getElementById('wizard-step-' + step);
    if (!pane) return true;

    // Check required fields within current step pane
    var requiredInputs = pane.querySelectorAll('input[required], select[required]');
    var isValid = true;
    var firstInvalid = null;

    requiredInputs.forEach(function(inp) {
        if (inp.type === 'checkbox') {
            if (!inp.checked) {
                isValid = false;
                if (!firstInvalid) firstInvalid = inp;
            }
        } else {
            if (!inp.value.trim()) {
                isValid = false;
                inp.classList.add('is-invalid');
                if (!firstInvalid) firstInvalid = inp;
            } else {
                inp.classList.remove('is-invalid');
            }
        }
    });

    // Step-specific quick validations
    if (step === 1) {
        var idField = document.getElementById('id_number');
        if (idField && idField.value.trim() && !/^\d{4}-\d{4}$/.test(idField.value.trim())) {
            idField.classList.add('is-invalid');
            var errSpan = document.getElementById('error-id_number');
            if (errSpan) errSpan.textContent = 'ID Number must follow the format xxxx-xxxx.';
            isValid = false;
            if (!firstInvalid) firstInvalid = idField;
        }

        var ageVal = parseInt(document.getElementById('age').value || '0', 10);
        if (ageVal < 18) {
            isValid = false;
            var errBdate = document.getElementById('error-birthdate');
            if (errBdate && !errBdate.textContent) errBdate.textContent = 'Must be at least 18 years old to register.';
            if (!firstInvalid) firstInvalid = document.getElementById('birthdate');
        }
    } else if (step === 4) {
        var p1 = document.getElementById('password').value;
        var p2 = document.getElementById('confirm_password').value;
        if (p1 && p2 && p1 !== p2) {
            document.getElementById('error-confirm_password').textContent = 'Passwords do not match.';
            isValid = false;
            if (!firstInvalid) firstInvalid = document.getElementById('confirm_password');
        }
    }

    if (!isValid && firstInvalid) {
        firstInvalid.focus();
    }
    return isValid;
}

function goToNextStep() {
    if (!validateCurrentStep(currentStep)) {
        return;
    }
    if (currentStep < totalSteps) {
        currentStep++;
        updateWizardUI();
    }
}

function goToPreviousStep() {
    if (currentStep > 1) {
        currentStep--;
        updateWizardUI();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (initialServerStep > 1) {
        currentStep = initialServerStep;
    }
    updateWizardUI();
});
</script>

<?php require_once __DIR__ . '/php/footer.php'; ?>
