<?php
/**
 * Two-Step Log-in Page
 * IT 107 - Information Assurance and Security 1
 * Implements Handout Section 8, Section 7 (CAPTCHA), and Protections 1, 3, 4, 5, 6
 */

$pageTitle = 'Log-in';
$extraJs = 'js/lockout.js';
require_once __DIR__ . '/php/header.php';
require_once __DIR__ . '/php/db.php';
require_once __DIR__ . '/php/security.php';
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/captcha.php';
require_once __DIR__ . '/php/logger.php';

// If already logged in, redirect to appropriate portal
if (isset($_SESSION['user_id'])) {
    if (($_SESSION['user_role'] ?? '') === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: assistance.php');
    }
    exit;
}

$errorMessage = '';
$infoMessage = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'verified') {
    $infoMessage = 'Your account has been verified! You may now sign in.';
} elseif (isset($_GET['msg']) && $_GET['msg'] === 'session_expired') {
    $errorMessage = 'Your session expired due to 15 minutes of inactivity. Please sign in again.';
} elseif (isset($_GET['msg']) && $_GET['msg'] === 'logged_out') {
    $infoMessage = 'You have successfully signed out.';
}

// 1. Protection 5: Check IP blocking status
$ipStatus = check_ip_status();
if ($ipStatus['is_blocked']) {
    $errorMessage = "Your IP address is temporarily blocked for security reasons. Time remaining: {$ipStatus['seconds_remaining']} seconds.";
}

// 2. Check Lockout Status for currently typed or remembered user
$lockoutState = [
    'is_locked' => false,
    'seconds_remaining' => 0,
    'show_forgot_password' => false
];

$typedUsername = trim($_POST['username'] ?? ($_GET['u'] ?? ''));

if ($typedUsername !== '') {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
    $stmt->execute([':u' => $typedUsername]);
    $user = $stmt->fetch();
    if ($user) {
        $lockoutState = check_account_lockout($user);
    }
}

// 3. Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errorMessage = 'Session security token invalid or expired. Please reload.';
    }
    // Blocked IP Check
    elseif ($ipStatus['is_blocked']) {
        $errorMessage = "Your IP address is temporarily blocked. Please wait {$ipStatus['seconds_remaining']} seconds.";
    }
    // Account Lockout Check
    elseif ($lockoutState['is_locked']) {
        $errorMessage = "This account is temporarily locked. Please wait {$lockoutState['seconds_remaining']} seconds.";
    }
    // CAPTCHA Verification
    elseif (!verify_captcha($_POST['captcha_code'] ?? '')) {
        log_activity('CAPTCHA_FAILED', $typedUsername, null, 'Login CAPTCHA failed.');
        $errorMessage = 'Incorrect security verification code. Please try again.';
    } else {
        $password = $_POST['password'] ?? '';

        $pdo = get_db_connection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $typedUsername]);
        $user = $stmt->fetch();

        if ($user) {
            // Re-verify lockout under fresh database state
            $lockoutState = check_account_lockout($user);

            if ($lockoutState['is_locked']) {
                $errorMessage = "This account is temporarily locked. Please wait {$lockoutState['seconds_remaining']} seconds.";
            } elseif (password_verify($password, $user['password_hash'])) {
                // Password is correct: Reset failures
                reset_account_failures($user['id_number']);

                // If user is not yet email-verified, re-issue registration OTP
                if (!$user['is_verified']) {
                    $fullName = $user['first_name'] . ' ' . $user['family_name'];
                    create_otp_for_user($user['id_number'], $user['email'], $fullName, 'register');

                    $_SESSION['pending_otp_user_id'] = $user['id_number'];
                    $_SESSION['pending_otp_purpose'] = 'register';
                    $_SESSION['pending_otp_email'] = $user['email'];
                    header('Location: verify_otp.php');
                    exit;
                }

                // Correct password: Send Log-in OTP (Handout 8.1: Do NOT create session yet)
                $fullName = $user['first_name'] . ' ' . $user['family_name'];
                create_otp_for_user($user['id_number'], $user['email'], $fullName, 'login');

                $_SESSION['pending_otp_user_id'] = $user['id_number'];
                $_SESSION['pending_otp_purpose'] = 'login';
                $_SESSION['pending_otp_email'] = $user['email'];

                header('Location: verify_otp.php');
                exit;

            } else {
                // Incorrect password for valid user: Record failure
                record_account_failure($user['id_number']);
                record_ip_failure();
                log_activity('LOGIN_FAILED', $typedUsername, $user['id_number'], 'Failed login: incorrect password.');

                // Refresh lockout state after recording failure
                $stmt->execute([':u' => $typedUsername]);
                $user = $stmt->fetch();
                $lockoutState = check_account_lockout($user);

                // Handout 8.1 Rule: Show ONLY "Invalid username or password."
                $errorMessage = 'Invalid username or password.';
            }
        } else {
            // Nonexistent username: Record IP failure
            record_ip_failure();
            log_activity('LOGIN_FAILED', $typedUsername, null, 'Failed login: nonexistent username.');

            // Handout 8.1 Rule: Exactly same message whether username exists or not (Protection 4)
            $errorMessage = 'Invalid username or password.';
        }
    }
}
?>

<style>
/* Modern Split-Layout Login Styling */
.login-split-card {
    display: grid;
    grid-template-columns: 1.1fr 1fr;
    background: #ffffff;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: 0 24px 60px rgba(50, 25, 5, 0.18), 0 4px 16px rgba(0,0,0,0.06);
    overflow: hidden;
    max-width: 1040px;
    margin: 32px auto 48px auto;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

@media (max-width: 860px) {
    .login-split-card {
        grid-template-columns: 1fr;
        max-width: 500px;
    }
    .login-info-side {
        display: none !important;
    }
}

.login-info-side {
    background: linear-gradient(160deg, rgba(20, 10, 4, 0.90) 0%, rgba(35, 18, 6, 0.88) 50%, rgba(12, 6, 2, 0.95) 100%),
                url('img/farmer-ricefield.jpg') center center / cover no-repeat;
    color: #ffffff;
    padding: 48px 42px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    border-right: 1px solid rgba(255, 255, 255, 0.12);
}

.login-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #d97706, #b45309);
    border: 1px solid #f59e0b;
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
    padding: 6px 14px;
    border-radius: 999px;
    letter-spacing: 0.6px;
    text-transform: uppercase;
    width: fit-content;
    box-shadow: 0 4px 14px rgba(217, 119, 6, 0.45);
}

.login-form-side {
    padding: 46px 42px;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-feature-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 16px;
    font-size: 13.5px;
    color: #e5e7eb;
    line-height: 1.5;
    background: rgba(255, 255, 255, 0.06);
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(4px);
    transition: background 0.2s ease, transform 0.2s ease;
}

.login-feature-item:hover {
    background: rgba(255, 255, 255, 0.12);
    transform: translateX(4px);
}

.login-feature-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: 1px solid #fbbf24;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
    color: #ffffff;
    box-shadow: 0 3px 10px rgba(217, 119, 6, 0.35);
}

.login-quick-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 14px;
}

.quick-chip {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    background: #f1ede6;
    border: 1px solid #ded3c5;
    color: var(--primary-dark);
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: default;
}

.pwd-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.pwd-wrapper input {
    padding-right: 74px;
}

.pwd-toggle-btn {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: #ece5dc;
    border: 1px solid #c9bdae;
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--text);
    cursor: pointer;
    transition: all 0.2s ease;
}

.pwd-toggle-btn:hover {
    background: #ded3c5;
    color: #000000;
}

.captcha-display-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(to right, #faf7f2, #f5efe6);
    border: 1.5px solid #cbbfb0;
    border-radius: var(--radius-md);
    padding: 10px 14px;
    margin-bottom: 10px;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.03);
}

.captcha-img-styled {
    height: 46px;
    border-radius: 6px;
    border: 1.5px solid #cbbfb0;
    background: #ffffff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.captcha-refresh-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 700;
    color: var(--primary-dark);
    background: #ffffff;
    border: 1.5px solid #cbbfb0;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}

.captcha-refresh-btn:hover {
    background: #f1ede6;
    border-color: var(--primary);
    color: var(--primary);
    transform: rotate(-2deg);
}
</style>

<div class="login-split-card">
    <!-- Left Visual / Security Feature Side -->
    <div class="login-info-side">
        <div>
            <div class="login-badge-pill">
                <span>&#127806;</span> Department of Agriculture Region XIII
            </div>
            <h2 style="font-family: 'Outfit', sans-serif; font-size: 29px; font-weight: 800; color: #ffffff; margin: 18px 0 10px 0; line-height: 1.25; text-shadow: 0 2px 6px rgba(0,0,0,0.6);">
                Caraga Farmers &amp; Producers Portal
            </h2>
            <p style="color: #f3f4f6; font-size: 13.5px; line-height: 1.6; margin-bottom: 24px; text-shadow: 0 1px 3px rgba(0,0,0,0.5);">
                Secure authenticated access for municipal subsidy disbursements, seed vouchers, fertilizer assistance, farm equipment loans, and agronomy advisory services.
            </p>

            <div class="login-feature-item">
                <div class="login-feature-icon">&#128272;</div>
                <div>
                    <strong style="color: #ffffff; font-size: 13.5px;">Field-Level AES-256-CBC Protection</strong><br>
                    <span style="color: #e5e7eb; font-size: 12.5px;">Sensitive mobile contact numbers are sealed at rest with unique cryptographic IVs.</span>
                </div>
            </div>

            <div class="login-feature-item">
                <div class="login-feature-icon">&#128273;</div>
                <div>
                    <strong style="color: #ffffff; font-size: 13.5px;">Out-of-Band 2-Step OTP Authentication</strong><br>
                    <span style="color: #e5e7eb; font-size: 12.5px;">A 6-digit verification code is generated via CSPRNG and dispatched directly to your verified email.</span>
                </div>
            </div>

            <div class="login-feature-item">
                <div class="login-feature-icon">&#128737;</div>
                <div>
                    <strong style="color: #ffffff; font-size: 13.5px;">Progressive Lockout &amp; IP Protection</strong><br>
                    <span style="color: #e5e7eb; font-size: 12.5px;">Tiered rate limiting (15s, 30s, 60s) and automated IP blocking defend against credential stuffing.</span>
                </div>
            </div>

            <div class="login-feature-item" style="margin-bottom: 0;">
                <div class="login-feature-icon">&#128220;</div>
                <div>
                    <strong style="color: #ffffff; font-size: 13.5px;">Immutable Audit Trails (RA 10173)</strong><br>
                    <span style="color: #e5e7eb; font-size: 12.5px;">Every login event, failure, and security anomaly is permanently recorded without edit or delete rights.</span>
                </div>
            </div>
        </div>

        <div style="border-top: 1px solid rgba(255, 255, 255, 0.18); padding-top: 18px; margin-top: 24px; font-size: 12px; color: #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
            <span>Caraga State University &middot; IT 107 IAS 1</span>
            <span style="font-weight: 800; color: #fef08a; background: rgba(0,0,0,0.3); padding: 3px 8px; border-radius: 4px;">ID: 2020-1050</span>
        </div>
    </div>

    <!-- Right Login Form Side -->
    <div class="login-form-side">
        <div style="margin-bottom: 22px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                <span style="font-size: 22px;">&#128274;</span>
                <h1 style="font-family: 'Outfit', sans-serif; font-size: 25px; font-weight: 800; color: var(--primary-dark); margin: 0;">
                    Portal Sign-in
                </h1>
            </div>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">
                Two-step authentication: Enter your password followed by email OTP.
            </p>

            <!-- Quick Roles Indicator Chips -->
            <div class="login-quick-chips">
                <span class="quick-chip">&#128104;&#8205;&#127806; Farmer Portal</span>
                <span class="quick-chip">&#128737;&#65039; Admin Dashboard</span>
                <span class="quick-chip">&#128272; RA 10173 Compliant</span>
            </div>
        </div>

        <?php if ($infoMessage): ?>
            <div class="alert alert-success">&#10003; <?= e($infoMessage) ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger">&#9888; <?= e($errorMessage) ?></div>
        <?php endif; ?>

        <!-- Progressive Lockout Countdown Box (Handout 8.3) -->
        <?php if ($lockoutState['is_locked']): ?>
            <div id="lockout-box" class="lockout-box" style="margin-bottom: 20px;">
                <p><strong>&#128274; Account Temporarily Locked (Tier <?= (int)$lockoutState['lockout_tier'] ?>)</strong></p>
                <p class="field-hint">Too many failed sign-in attempts. You must wait before trying again:</p>
                <div id="lockout-countdown" class="lockout-timer"><?= (int)$lockoutState['seconds_remaining'] ?></div>
                <p class="field-hint">seconds remaining</p>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" id="login-form">
            <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">

            <div class="form-group">
                <label for="username" class="form-label">Username<span class="required-star">*</span></label>
                <input type="text" id="username" name="username" class="form-control"
                       maxlength="20" value="<?= e($typedUsername) ?>" placeholder="e.g. farmer_juan" required
                       <?= $lockoutState['is_locked'] ? 'readonly' : '' ?>>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password<span class="required-star">*</span></label>
                <div class="pwd-wrapper">
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="Enter your account password" required
                           <?= $lockoutState['is_locked'] ? 'readonly' : '' ?>>
                    <button type="button" id="toggle-pwd-btn" class="pwd-toggle-btn"
                            onclick="let p=document.getElementById('password'); if(p.type==='password'){p.type='text'; this.textContent='Hide';}else{p.type='password'; this.textContent='Show';}">
                        Show
                    </button>
                </div>
            </div>

            <!-- CAPTCHA Two-Row Layout -->
            <div class="form-group">
                <label for="captcha_code" class="form-label">Security Verification (CAPTCHA)<span class="required-star">*</span></label>
                <!-- Row 1: Image & Refresh Button -->
                <div class="captcha-display-box">
                    <img src="php/captcha_image.php?t=<?= time() ?>" id="login-captcha-img" class="captcha-img-styled" alt="CAPTCHA">
                    <button type="button" class="captcha-refresh-btn" onclick="document.getElementById('login-captcha-img').src='php/captcha_image.php?t='+new Date().getTime()">
                        <span>&#8634;</span> Refresh Code
                    </button>
                </div>
                <!-- Row 2: Full-width Input Field -->
                <input type="text" id="captcha_code" name="captcha_code" class="form-control"
                       placeholder="Enter the security code shown above" maxlength="6" required <?= $lockoutState['is_locked'] ? 'readonly' : '' ?>>
                <span class="field-hint">Case-insensitive. Single-use bot protection.</span>
            </div>

            <div style="margin-top: 24px;">
                <button type="submit" id="login-btn" class="btn btn-primary" style="width: 100%; padding: 13.5px; font-size: 14.5px; font-weight: 800; letter-spacing: 0.3px; box-shadow: 0 4px 14px rgba(194, 94, 0, 0.35);"
                        <?= ($lockoutState['is_locked'] || $ipStatus['is_blocked']) ? 'disabled' : '' ?>>
                    Proceed to Two-Step Verification &#8594;
                </button>
            </div>
        </form>

        <!-- Forgot Password / Register Links -->
        <div style="margin-top: 24px; text-align: center; border-top: 1px solid var(--border); padding-top: 18px; font-size: 13px;">
            <?php if ($lockoutState['show_forgot_password']): ?>
                <p style="margin-bottom: 10px;">
                    <a href="forgot_password.php?u=<?= urlencode($typedUsername) ?>" style="color: var(--danger); font-weight: 700;">
                        &#128273; Forgot Password? Reset Here
                    </a>
                </p>
            <?php endif; ?>

            <p style="color: var(--text-muted); margin: 0;">
                Don't have an agricultural account?
                <a href="register.php" id="register-link" style="color: var(--primary); font-weight: 700; text-decoration: underline;">
                    Register as Farmer / Producer
                </a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/php/footer.php'; ?>
