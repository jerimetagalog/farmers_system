<?php
/**
 * Application Home & Portal Page
 * IT 107 - Information Assurance and Security 1
 * Secure Farmers Registration, Assistance and Monitoring System
 * Student ID: 2020-1050
 */

$pageTitle = 'Home';
require_once __DIR__ . '/php/header.php';
?>

<style>
/* Modern Home Page UI with Real Landscape Farmer Hero Banner */
.hero-card {
    background: linear-gradient(135deg, rgba(15, 8, 2, 0.45) 0%, rgba(20, 10, 3, 0.35) 50%, rgba(15, 8, 2, 0.50) 100%),
                url('img/farmer-ricefield.jpg') center 40% / cover no-repeat;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 56px 40px;
    box-shadow: 0 16px 45px rgba(20, 10, 2, 0.16);
    position: relative;
    overflow: hidden;
    margin-top: 6px;
    margin-bottom: 28px;
}

.hero-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 50% 40%, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.45) 90%);
    pointer-events: none;
}

.hero-badge-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(217, 119, 6, 0.9);
    color: #ffffff;
    border: 1px solid #f59e0b;
    font-size: 12px;
    font-weight: 800;
    padding: 6px 16px;
    border-radius: 999px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
}

.hero-title {
    font-family: 'Outfit', sans-serif;
    font-size: 38px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    margin-bottom: 16px;
    letter-spacing: -0.5px;
    text-shadow: 0 3px 8px rgba(0, 0, 0, 0.6);
}

.hero-title .highlight {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #fde68a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-description {
    font-size: 16.5px;
    color: #f3f4f6;
    max-width: 740px;
    margin: 0 auto 30px auto;
    line-height: 1.65;
    text-shadow: 0 2px 5px rgba(0, 0, 0, 0.6);
}

.hero-cta-group {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 10px;
}

.hero-cta-btn {
    padding: 14px 32px;
    font-size: 15px;
    font-weight: 800;
    border-radius: 12px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.25s ease;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
}

.welcome-banner {
    background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
    border: 1px solid #fde68a;
    border-radius: var(--radius-lg);
    padding: 24px;
    margin-top: 20px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.08);
}

/* ==========================================================================
   INTERACTIVE SHOWCASE & TWO-COLUMN PORTAL SECTION
   ========================================================================== */
.portal-main-grid {
    display: grid;
    grid-template-columns: 1.65fr 1fr;
    gap: 24px;
    margin-top: 10px;
}

@media (max-width: 900px) {
    .portal-main-grid {
        grid-template-columns: 1fr;
    }
}

/* Showcase Card with Tabs */
.showcase-card {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.showcase-header {
    background: #ffffff;
    border-bottom: 1px solid var(--border);
    padding: 18px 24px 0 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.tab-nav {
    display: flex;
    gap: 6px;
}

.tab-btn {
    background: transparent;
    border: none;
    outline: none;
    font-family: inherit;
    font-size: 14px;
    font-weight: 700;
    color: var(--text-muted);
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.tab-btn:hover {
    color: var(--primary);
}

.tab-btn.active {
    color: var(--primary-dark);
    border-bottom-color: var(--primary);
    background: var(--primary-subtle);
    border-radius: 6px 6px 0 0;
}

.showcase-body {
    padding: 24px;
    flex: 1;
}

.tab-content {
    display: none;
    animation: fadeIn 0.25s ease-in-out;
}

.tab-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Program Card Items */
.program-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 14px 16px;
    border-radius: var(--radius-md);
    background: var(--bg-page);
    border: 1px solid var(--border);
    margin-bottom: 12px;
    transition: transform 0.15s ease, background-color 0.15s ease;
}

.program-item:hover {
    background: #ffffff;
    transform: translateX(4px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.04);
}

.program-icon-badge {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #fef3c7;
    border: 1px solid #fde68a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.program-title {
    font-weight: 700;
    color: var(--primary-dark);
    font-size: 14.5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.program-tag {
    font-size: 10.5px;
    padding: 2px 7px;
    border-radius: 10px;
    font-weight: 700;
    text-transform: uppercase;
}

.tag-active {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}

.tag-subsidy {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

.program-desc {
    font-size: 13px;
    color: var(--text-muted);
    margin-top: 3px;
    line-height: 1.45;
}

/* Security Protection Item */
.sec-card-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px;
    border-radius: var(--radius-md);
    background: var(--bg-page);
    border: 1px solid var(--border);
    margin-bottom: 12px;
}

.sec-icon-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

/* Sidebar Cards */
.side-card {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    padding: 22px;
    margin-bottom: 20px;
}

.side-card-title {
    font-family: 'Outfit', sans-serif;
    font-size: 16.5px;
    font-weight: 700;
    color: var(--primary-dark);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Progress / Quota trackers */
.quota-meter {
    margin-bottom: 16px;
}

.quota-label-group {
    display: flex;
    justify-content: space-between;
    font-size: 12.5px;
    font-weight: 600;
    margin-bottom: 6px;
    color: var(--text);
}

.progress-track {
    width: 100%;
    height: 9px;
    background: #f3efe9;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.progress-fill {
    height: 100%;
    border-radius: 10px;
    transition: width 0.5s ease;
}

.fill-amber {
    background: linear-gradient(90deg, #f59e0b, #d97706);
}

.fill-green {
    background: linear-gradient(90deg, #10b981, #059669);
}

.fill-blue {
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
}

/* Bulletin Notification */
.bulletin-box {
    background: #fefce8;
    border: 1px solid #fef08a;
    border-radius: var(--radius-md);
    padding: 14px;
    margin-bottom: 14px;
}

.bulletin-heading {
    font-size: 12.5px;
    font-weight: 700;
    color: #854d0e;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.bulletin-text {
    font-size: 12px;
    color: #713f12;
    line-height: 1.5;
    margin: 0;
}

/* Office Hours schedule box */
.schedule-pill-box {
    background: var(--bg-page);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 12px 14px;
    font-size: 13px;
    line-height: 1.6;
}
</style>

<!-- Hero Section -->
<div class="hero-card">
    <div style="text-align: center;">
        <div class="hero-badge-tag">
            <span>&#127806;</span> Department of Agriculture Region XIII &middot; Caraga
        </div>
        <h1 class="hero-title">
            Secure Farmers Registration &amp; <span class="highlight">Assistance Portal</span>
        </h1>
        <p class="hero-description">
            The official agricultural management and assistance system designed for local farmers and producers. Apply for subsidies, access certified seeds and fertilizers, request equipment, and access municipal agricultural services with enterprise-grade data protection.
        </p>
    </div>

    <?php if (!$isLoggedIn): ?>
        <div class="hero-cta-group">
            <a href="register.php" class="btn btn-primary hero-cta-btn">
                <span>&#127806;</span> Register as Farmer / Producer
            </a>
            <a href="login.php" class="btn btn-accent hero-cta-btn">
                <span>&#128274;</span> Agricultural Portal Log-in
            </a>
        </div>
    <?php else: ?>
        <div class="welcome-banner">
            <h3 style="color: var(--primary-dark); font-size: 20px; font-weight: 700; margin-bottom: 6px;">
                Welcome back, <?= e($_SESSION['user_name'] ?? 'Farmer') ?>!
            </h3>
            <p style="color: var(--secondary); font-size: 14.5px; margin-bottom: 18px;">
                You are authenticated as <strong><?= e(ucfirst($_SESSION['user_role'] ?? 'farmer')) ?></strong> (User ID: <code><?= e($_SESSION['user_id'] ?? '') ?></code>).
            </p>
            <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
                <a href="assistance.php" class="btn btn-primary" style="padding: 12px 24px;">
                    &#127806; Access Farm Assistance Requests
                </a>
                <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                    <a href="admin_dashboard.php" class="btn btn-accent" style="padding: 12px 24px;">
                        &#128737; Agricultural Security Dashboard
                    </a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-secondary" style="padding: 12px 24px;">
                    Log-out
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- MAIN MODERN SHOWCASE GRID -->
<div class="portal-main-grid">
    <!-- Left Column: Interactive Tabbed Showcase -->
    <div class="showcase-card">
        <div class="showcase-header">
            <div class="tab-nav">
                <button type="button" class="tab-btn active" onclick="switchPortalTab(event, 'tab-programs')">
                    &#127806; Support Programs
                </button>
                <button type="button" class="tab-btn" onclick="switchPortalTab(event, 'tab-security')">
                    &#128274; Security Architecture
                </button>
                <button type="button" class="tab-btn" onclick="switchPortalTab(event, 'tab-advisory')">
                    &#127793; Crop Advisory
                </button>
            </div>
            <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
                Municipal Hub 2026
            </span>
        </div>

        <div class="showcase-body">
            <!-- TAB 1: Support Programs -->
            <div id="tab-programs" class="tab-content active">
                <div class="program-item">
                    <div class="program-icon-badge">&#127806;</div>
                    <div style="flex: 1;">
                        <div class="program-title">
                            Fertilizer &amp; Certified Seed Subsidy
                            <span class="program-tag tag-active">Active Window</span>
                        </div>
                        <div class="program-desc">
                            High-yielding inbred &amp; hybrid palay varieties alongside organic soil conditioners distributed per hectare allocation.
                        </div>
                    </div>
                </div>

                <div class="program-item">
                    <div class="program-icon-badge">&#128737;</div>
                    <div style="flex: 1;">
                        <div class="program-title">
                            PCIC Crop Insurance Coverage
                            <span class="program-tag tag-subsidy">Zero Premium</span>
                        </div>
                        <div class="program-desc">
                            Guaranteed protection against typhoon inundation, drought spells, and pest infesting certified rice, corn, and rootcrop plots.
                        </div>
                    </div>
                </div>

                <div class="program-item">
                    <div class="program-icon-badge">&#9881;</div>
                    <div style="flex: 1;">
                        <div class="program-title">
                            Farm Mechanization &amp; Solar Irrigation
                            <span class="program-tag tag-active">Open Application</span>
                        </div>
                        <div class="program-desc">
                            Grant assistance for solar-powered shallow tube wells, walk-behind transplanters, and cooperative multi-pass milling machinery.
                        </div>
                    </div>
                </div>

                <div class="program-item">
                    <div class="program-icon-badge">&#128300;</div>
                    <div style="flex: 1;">
                        <div class="program-title">
                            Soil Health Analysis &amp; Agronomy
                            <span class="program-tag tag-subsidy">Free Testing</span>
                        </div>
                        <div class="program-desc">
                            Rapid chemical N-P-K nutrient evaluation and micro-biological pest diagnostics at the municipal agricultural diagnostics center.
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Security Framework -->
            <div id="tab-security" class="tab-content">
                <div class="sec-card-item">
                    <div class="sec-icon-circle">&#128272;</div>
                    <div>
                        <strong style="color: var(--primary-dark); font-size: 14px;">AES-256-CBC Field-Level Encryption</strong>
                        <p class="program-desc">
                            Farmer mobile phone numbers and sensitive contact records are encrypted with random 16-byte initialization vectors prior to SQL persistence.
                        </p>
                    </div>
                </div>

                <div class="sec-card-item">
                    <div class="sec-icon-circle">&#128273;</div>
                    <div>
                        <strong style="color: var(--primary-dark); font-size: 14px;">Two-Step Authentication with Out-of-Band OTP</strong>
                        <p class="program-desc">
                            Password authentication is chained to a single-use 6-digit one-time password delivered through encrypted SMTP, expiring within 300 seconds.
                        </p>
                    </div>
                </div>

                <div class="sec-card-item">
                    <div class="sec-icon-circle">&#9888;</div>
                    <div>
                        <strong style="color: var(--primary-dark); font-size: 14px;">Progressive Brute-Force Rate Limiting</strong>
                        <p class="program-desc">
                            Tiered cooldown windows (15s, 30s, 60s) and automatic sliding-window IP blocking protect portal endpoints against credential stuffing.
                        </p>
                    </div>
                </div>

                <div class="sec-card-item">
                    <div class="sec-icon-circle">&#128220;</div>
                    <div>
                        <strong style="color: var(--primary-dark); font-size: 14px;">Append-Only Immutable Audit Engine &amp; RA 10173</strong>
                        <p class="program-desc">
                            Every authentication attempt, assistance submission, and privilege elevation event is sealed in an append-only audit trail in compliance with the Philippine Data Privacy Act.
                        </p>
                    </div>
                </div>
            </div>

            <!-- TAB 3: Crop Advisory -->
            <div id="tab-advisory" class="tab-content">
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: var(--radius-md); padding: 18px; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <span style="font-size: 22px;">&#127783;</span>
                        <h4 style="color: #166534; font-size: 15px; margin: 0; font-weight: 700;">Seasonal Planting Calendar (Wet Crop Season)</h4>
                    </div>
                    <p style="font-size: 13px; color: #14532d; line-height: 1.6; margin: 0;">
                        Caraga basin agricultural extension advises prioritizing flood-tolerant palay strains (Submarino 1 &amp; NSIC Rc 68) during upcoming rainfall cycles.
                    </p>
                </div>

                <div class="program-item">
                    <div class="program-icon-badge" style="background: #f0fdf4; border-color: #bbf7d0;">&#128027;</div>
                    <div style="flex: 1;">
                        <div class="program-title">Integrated Pest Management Bulletin</div>
                        <div class="program-desc">
                            Brown planthopper surveillance is active in lowland irrigated plots. Check water level rotation guidelines at your barangay extension desk.
                        </div>
                    </div>
                </div>

                <div class="program-item">
                    <div class="program-icon-badge" style="background: #eff6ff; border-color: #bfdbfe;">&#9728;</div>
                    <div style="flex: 1;">
                        <div class="program-title">Soil Conditioning Advisory</div>
                        <div class="program-desc">
                            Apply decomposed agricultural lime before the second harrowing pass to balance acidic soils common in high-precipitation zones.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Assistance Allocation & Municipal Desk -->
    <div>
        <!-- Distribution Allocation Progress -->
        <div class="side-card">
            <h3 class="side-card-title">
                <span>&#128202;</span> 2026 Subsidy Program Allocations
            </h3>

            <div class="quota-meter">
                <div class="quota-label-group">
                    <span>Palay Certified Seed Grant</span>
                    <strong style="color: var(--primary);">78% Disbursed</strong>
                </div>
                <div class="progress-track">
                    <div class="progress-fill fill-amber" style="width: 78%;"></div>
                </div>
            </div>

            <div class="quota-meter">
                <div class="quota-label-group">
                    <span>Fertilizer Discount Vouchers</span>
                    <strong style="color: var(--accent);">92% Disbursed</strong>
                </div>
                <div class="progress-track">
                    <div class="progress-fill fill-green" style="width: 92%;"></div>
                </div>
            </div>

            <div class="quota-meter" style="margin-bottom: 0;">
                <div class="quota-label-group">
                    <span>Solar Water Pump Installations</span>
                    <strong style="color: #2563eb;">64% Completed</strong>
                </div>
                <div class="progress-track">
                    <div class="progress-fill fill-blue" style="width: 64%;"></div>
                </div>
            </div>
        </div>

        <!-- Municipal Office Desk & Bulletin -->
        <div class="side-card">
            <h3 class="side-card-title">
                <span>&#128205;</span> Municipal Agriculture Helpdesk
            </h3>

            <div class="bulletin-box">
                <div class="bulletin-heading">
                    <span>&#128227;</span> Official Notice
                </div>
                <p class="bulletin-text">
                    Assistance application validation runs weekly. Registered producers may check submission status directly under their Farm Assistance tab.
                </p>
            </div>

            <div class="schedule-pill-box">
                <div style="font-weight: 700; color: var(--secondary); margin-bottom: 4px;">
                    &#128343; Consultation Hours:
                </div>
                <div style="color: var(--text);">
                    <strong>Monday &ndash; Friday</strong><br>
                    8:00 AM &ndash; 12:00 PM &nbsp;|&nbsp; 1:00 PM &ndash; 5:00 PM
                </div>
            </div>

            <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed var(--border); font-size: 11.5px; color: var(--text-muted); line-height: 1.6;">
                <div><strong>Caraga State University</strong> &middot; IT 107 IAS 1</div>
                <div>Student ID: <strong>2020-1050</strong></div>
                <div style="margin-top: 4px; color: var(--accent); font-weight: 700;">&#9679; Security Daemon: Online &amp; Auditing</div>
            </div>
        </div>
    </div>
</div>

<script>
function switchPortalTab(evt, tabId) {
    var tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(function(t) {
        t.classList.remove('active');
    });

    var btns = document.querySelectorAll('.tab-btn');
    btns.forEach(function(b) {
        b.classList.remove('active');
    });

    var targetTab = document.getElementById(tabId);
    if (targetTab) {
        targetTab.classList.add('active');
    }

    if (evt && evt.currentTarget) {
        evt.currentTarget.classList.add('active');
    }
}
</script>

<?php require_once __DIR__ . '/php/footer.php'; ?>
