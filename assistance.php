<?php
/**
 * Agricultural Assistance Request & Field Support Portal
 * IT 107 - Information Assurance and Security 1
 * Implements System Core Functionality & Protection 13 (Decryption of sensitive contact field)
 * Primary Student ID: 2020-1050
 */

$pageTitle = 'Agricultural Assistance & Programs';
require_once __DIR__ . '/php/header.php';
require_once __DIR__ . '/php/db.php';
require_once __DIR__ . '/php/security.php';
require_once __DIR__ . '/php/logger.php';

// Protection 6 & Handout 8.4: Session check & cache prevention
prevent_browser_cache();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? 'farmer';
$pdo = get_db_connection();

// Fetch current user details
$userStmt = $pdo->prepare("SELECT * FROM `users` WHERE `id_number` = :id");
$userStmt->execute([':id' => $userId]);
$user = $userStmt->fetch();

if (!$user) {
    header('Location: logout.php');
    exit;
}

// Protection 13: Decrypt contact number at rest ONLY for display to its owner or admin
$decryptedContact = decrypt_sensitive_field($user['contact_number_encrypted'] ?? '');

$successMessage = '';
$errorMessage = '';

// Allowed Agricultural Programs
$allowedPrograms = [
    'Fertilizer & Seed Subsidy',
    'Crop Insurance & Protection',
    'Farm Equipment Loan',
    'Soil & Agronomy Advisory',
    'Irrigation Support'
];

// Handle Assistance Request Submission (Farmer)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_assistance') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errorMessage = 'Invalid security token. Please refresh the page.';
    } else {
        $prog = trim($_POST['program_category'] ?? '');
        $date = trim($_POST['preferred_date'] ?? '');
        $farmLoc = trim($_POST['farm_location'] ?? '');
        $details = trim($_POST['request_details'] ?? '');

        if (!in_array($prog, $allowedPrograms, true)) {
            $errorMessage = 'Select a valid agricultural assistance program.';
        } elseif (empty($date) || empty($farmLoc)) {
            $errorMessage = 'Please provide the preferred date and farm location.';
        } elseif (strtotime($date) < strtotime('today')) {
            $errorMessage = 'Preferred target date cannot be in the past.';
        } elseif (empty($details)) {
            $errorMessage = 'Please specify details of your farm assistance request.';
        } else {
            $now = date('Y-m-d H:i:s');
            $insStmt = $pdo->prepare("
                INSERT INTO `assistance_requests` (
                    `farmer_id`, `program_category`, `preferred_date`, `farm_location`,
                    `request_details`, `status`, `created_at`, `updated_at`
                ) VALUES (
                    :fid, :prog, :pdate, :floc, :details, 'Pending', :created, :updated
                )
            ");
            $insStmt->execute([
                ':fid'     => $userId,
                ':prog'    => $prog,
                ':pdate'   => $date,
                ':floc'    => $farmLoc,
                ':details' => $details,
                ':created' => $now,
                ':updated' => $now
            ]);

            log_activity('ASSISTANCE_REQUESTED', $user['username'], $userId, "Filed assistance request: {$prog} for target date {$date}.");
            $successMessage = 'Your agricultural assistance request has been submitted successfully! Current Status: Pending.';
        }
    }
}

// Handle Status Updates (Admin or Farmer cancellation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errorMessage = 'Invalid security token.';
    } else {
        $reqId = (int)($_POST['request_id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? '');
        $notes = trim($_POST['officer_notes'] ?? '');
        $allowedStatuses = ['Pending', 'Approved', 'Dispatched', 'Rejected'];

        if (!in_array($newStatus, $allowedStatuses, true)) {
            $errorMessage = 'Invalid status selected.';
        } else {
            if ($userRole === 'admin') {
                $updStmt = $pdo->prepare("
                    UPDATE `assistance_requests` 
                    SET `status` = :st, `officer_notes` = :notes, `updated_at` = NOW() 
                    WHERE `id` = :id
                ");
                $updStmt->execute([
                    ':st'    => $newStatus,
                    ':notes' => $notes !== '' ? $notes : null,
                    ':id'    => $reqId
                ]);
                log_activity('ASSISTANCE_STATUS_UPDATED', $user['username'], $userId, "Admin updated request #{$reqId} status to {$newStatus}.");
                $successMessage = "Assistance Request #{$reqId} has been updated to '{$newStatus}'.";
            } else {
                // Farmer: Can only reject/cancel THEIR OWN pending request
                $cancelStmt = $pdo->prepare("
                    UPDATE `assistance_requests` 
                    SET `status` = 'Rejected', `officer_notes` = 'Cancelled by farmer', `updated_at` = NOW() 
                    WHERE `id` = :id AND `farmer_id` = :fid AND `status` = 'Pending'
                ");
                $cancelStmt->execute([':id' => $reqId, ':fid' => $userId]);

                if ($cancelStmt->rowCount() > 0) {
                    log_activity('ASSISTANCE_CANCELLED', $user['username'], $userId, "Farmer cancelled request #{$reqId}.");
                    $successMessage = "Assistance request #{$reqId} has been withdrawn.";
                } else {
                    $errorMessage = 'Unable to cancel this request. It may already be approved or does not belong to you.';
                }
            }
        }
    }
}

// Retrieve Assistance Requests
if ($userRole === 'admin') {
    $listStmt = $pdo->query("
        SELECT r.*, u.first_name, u.family_name, u.farmer_type, u.primary_crop, u.email, u.contact_number_encrypted
        FROM `assistance_requests` r
        JOIN `users` u ON r.farmer_id = u.id_number
        ORDER BY r.preferred_date DESC, r.created_at DESC
    ");
    $requests = $listStmt->fetchAll();
} else {
    $listStmt = $pdo->prepare("
        SELECT * FROM `assistance_requests`
        WHERE `farmer_id` = :fid
        ORDER BY `preferred_date` DESC, `created_at` DESC
    ");
    $listStmt->execute([':fid' => $userId]);
    $requests = $listStmt->fetchAll();
}
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-bottom: 2px solid var(--border); padding-bottom: 14px; margin-bottom: 22px;">
        <div>
            <h1 class="card-title" style="margin-bottom: 4px;">
                <?= $userRole === 'admin' ? '&#127806; Agricultural Assistance Management' : '&#127806; My Agricultural Assistance Requests' ?>
            </h1>
            <p class="field-hint" style="margin-top: 0;">
                User: <strong><?= e($user['first_name'] . ' ' . $user['family_name']) ?></strong> (ID: <?= e($user['id_number']) ?>) &middot;
                Type: <strong><?= e($user['farmer_type'] ?? 'Farmer') ?></strong> &middot;
                Commodity: <strong><?= e($user['primary_crop'] ?? 'N/A') ?></strong> &middot;
                Contact: <strong><?= e($decryptedContact) ?></strong> (AES-256 Decrypted)
            </p>
        </div>
        <?php if ($userRole !== 'admin'): ?>
            <a href="#request-form" class="btn btn-primary btn-sm">&#43; Request Farm Assistance</a>
        <?php endif; ?>
    </div>

    <?php if ($successMessage): ?>
        <div class="alert alert-success">&#10003; <?= e($successMessage) ?></div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger">&#9888; <?= e($errorMessage) ?></div>
    <?php endif; ?>

    <!-- REQUESTS LIST TABLE -->
    <h3 style="color: var(--secondary); font-size: 16px; margin: 18px 0 10px 0;">Assistance Program Applications</h3>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <?php if ($userRole === 'admin'): ?>
                        <th>Farmer Details</th>
                        <th>Contact</th>
                    <?php endif; ?>
                    <th>Program Category</th>
                    <th>Target Date</th>
                    <th>Farm Location</th>
                    <th>Request Details</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="<?= $userRole === 'admin' ? '8' : '6' ?>" style="text-align: center; color: var(--text-muted); padding: 26px;">
                            No agricultural assistance requests found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?= (int)$req['id'] ?></td>
                            <?php if ($userRole === 'admin'): ?>
                                <td>
                                    <strong><?= e($req['first_name'] . ' ' . $req['family_name']) ?></strong><br>
                                    <span style="font-size: 11px; color: var(--text-muted);"><?= e($req['farmer_id']) ?> &middot; <?= e($req['farmer_type']) ?></span>
                                </td>
                                <td><?= e(decrypt_sensitive_field($req['contact_number_encrypted'] ?? '')) ?></td>
                            <?php endif; ?>
                            <td><strong><?= e($req['program_category']) ?></strong></td>
                            <td><?= e($req['preferred_date']) ?></td>
                            <td><?= e($req['farm_location']) ?></td>
                            <td>
                                <?= nl2br(e($req['request_details'])) ?>
                                <?php if (!empty($req['officer_notes'])): ?>
                                    <div style="margin-top: 6px; padding: 4px 8px; background: rgba(0,0,0,0.03); border-left: 2px solid var(--accent); font-size: 11px; color: var(--text);">
                                        <strong>Officer Note:</strong> <?= e($req['officer_notes']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $badgeClass = 'badge-pending';
                                if ($req['status'] === 'Approved') $badgeClass = 'badge-confirmed';
                                elseif ($req['status'] === 'Dispatched') $badgeClass = 'badge-completed';
                                elseif ($req['status'] === 'Rejected') $badgeClass = 'badge-cancelled';
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= e($req['status']) ?></span>
                            </td>
                            <td>
                                <?php if ($userRole === 'admin'): ?>
                                    <form method="POST" action="assistance.php" style="display: flex; flex-direction: column; gap: 4px;">
                                        <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="request_id" value="<?= (int)$req['id'] ?>">
                                        <div style="display: flex; gap: 4px;">
                                            <select name="status" class="form-control" style="padding: 4px 6px; font-size: 11.5px; width: 105px;">
                                                <option value="Pending" <?= $req['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="Approved" <?= $req['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                                                <option value="Dispatched" <?= $req['status'] === 'Dispatched' ? 'selected' : '' ?>>Dispatched</option>
                                                <option value="Rejected" <?= $req['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                            </select>
                                            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 11px;">Save</button>
                                        </div>
                                        <input type="text" name="officer_notes" class="form-control" style="padding: 3px 6px; font-size: 11px;" placeholder="Add remarks..." value="<?= e($req['officer_notes'] ?? '') ?>">
                                    </form>
                                <?php else: ?>
                                    <?php if ($req['status'] === 'Pending'): ?>
                                        <form method="POST" action="assistance.php" onsubmit="return confirm('Withdraw this assistance request?');">
                                            <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="request_id" value="<?= (int)$req['id'] ?>">
                                            <input type="hidden" name="status" value="Rejected">
                                            <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--danger); border-color: var(--danger); padding: 4px 8px; font-size: 11px;">
                                                Withdraw
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="font-size: 12px; color: var(--text-muted);">&mdash;</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($userRole !== 'admin'): ?>
<!-- FARMER ASSISTANCE APPLICATION FORM -->
<div class="card" id="request-form" style="max-width: 760px; margin: 30px auto;">
    <h2 class="card-title">&#127806; Apply for Agricultural Support Program</h2>
    <p class="card-subtitle">Select assistance type, target delivery date, and specify farm requirements.</p>

    <form method="POST" action="assistance.php">
        <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">
        <input type="hidden" name="action" value="request_assistance">

        <div class="form-row">
            <div class="form-group">
                <label for="program_category" class="form-label">Support Program<span class="required-star">*</span></label>
                <select id="program_category" name="program_category" class="form-control" required>
                    <option value="">-- Choose Program --</option>
                    <?php foreach ($allowedPrograms as $p): ?>
                        <option value="<?= e($p) ?>"><?= e($p) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="preferred_date" class="form-label">Target Implementation Date<span class="required-star">*</span></label>
                <input type="date" id="preferred_date" name="preferred_date" class="form-control"
                       min="<?= date('Y-m-d') ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="farm_location" class="form-label">Farm Location (Purok/Barangay/Sitio)<span class="required-star">*</span></label>
                <input type="text" id="farm_location" name="farm_location" class="form-control"
                       value="<?= e($user['purok_street'] . ', ' . $user['barangay'] . ', ' . $user['municipality_city']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Contact Mobile (AES-256 Encrypted at Rest)</label>
                <input type="text" class="form-control" value="<?= e($decryptedContact) ?>" readonly>
                <span class="field-hint">Used for agricultural field officer contact.</span>
            </div>
        </div>

        <div class="form-group">
            <label for="request_details" class="form-label">Farm Details &amp; Requirements<span class="required-star">*</span></label>
            <textarea id="request_details" name="request_details" rows="3" class="form-control" placeholder="Specify land area in hectares, variety needed, crop condition, or required farm inputs..." required></textarea>
        </div>

        <div style="margin-top: 20px; text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 28px;">
                Submit Support Request &#8594;
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/php/footer.php'; ?>
