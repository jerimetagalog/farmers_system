-- ========================================================
-- IT 107 - Information Assurance and Security 1
-- Final Laboratory Project: Secure Farmers Registration, Assistance and Monitoring System
-- Database Seed Data: farmers_system_db
-- Primary Student ID: 2020-1050
-- ========================================================

USE `farmers_system_db`;

-- Clear existing data
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `activity_logs`;
TRUNCATE TABLE `security_flags`;
TRUNCATE TABLE `assistance_requests`;
TRUNCATE TABLE `password_history`;
TRUNCATE TABLE `ip_blocks`;
TRUNCATE TABLE `otps`;
TRUNCATE TABLE `authentication_answers`;
TRUNCATE TABLE `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. SEED USERS
-- Admin: admin_officer (ID: 2020-1050) / Password: Admin@2026!
-- Farmer 1: farmer_juan (ID: 2020-1051) / Password: Farmer@2026!
-- Farmer 2: farmer_maria (ID: 2020-1052) / Password: Farmer@2026!

INSERT INTO `users` (
    `id_number`, `first_name`, `middle_name`, `family_name`, `name_extension`,
    `birthdate`, `age`, `purok_street`, `barangay`, `municipality_city`, `province`, `country`, `zip_code`,
    `email`, `username`, `password_hash`, `contact_number_encrypted`,
    `farmer_type`, `primary_crop`, `emergency_contact_encrypted`,
    `role`, `is_verified`, `failed_logins`, `lockout_tier`, `locked_until`, `privacy_consent_at`, `created_at`, `updated_at`
) VALUES
(
    '2020-1050', 'Efren', 'V', 'Castillano', NULL,
    '1998-05-12', 28, 'Purok 4', 'Ampayon', 'Butuan City', 'Agusan del Norte', 'Philippines', '8600',
    'efren.castillano@csucc.edu.ph', 'admin_officer',
    '$2y$10$wYd22L6DS5tBxeI4sqZ2OuUv.LsfW1HEyqSRUrH.oXUEtvvxb.CgO',
    'AzNsJtCdz5HdrvIREXXmbuORCjpAE1Pmz+hQqJZJ0FE=',
    'Commercial Farmer', 'Rice', NULL,
    'admin', 1, 0, 0, NULL, '2026-11-19 08:00:00', '2026-11-19 08:00:00', '2026-11-19 08:00:00'
),
(
    '2020-1051', 'Juan', 'Dela', 'Cruz', NULL,
    '1985-08-20', 41, 'Purok 2 Green Valley', 'Los Angeles', 'Butuan City', 'Agusan del Norte', 'Philippines', '8600',
    'efren.castillano+juan@csucc.edu.ph', 'farmer_juan',
    '$2y$10$sXx4NxMfGnkGLKoOsjJpneD2GmHeCRHh6j2/LoDdHv6lRqU/Ojlai',
    '0M902eJrITdk69lgg0rue4IRwkTgNvvimqHDNjGYWd8=',
    'Smallholder Farmer', 'Rice', NULL,
    'farmer', 1, 0, 0, NULL, '2026-11-19 08:15:00', '2026-11-19 08:15:00', '2026-11-19 08:15:00'
),
(
    '2020-1052', 'Maria', 'Santos', 'Reyes', NULL,
    '1990-11-14', 36, 'Purok 5 Riverside', 'Baan', 'Butuan City', 'Agusan del Norte', 'Philippines', '8600',
    'efren.castillano+maria@csucc.edu.ph', 'farmer_maria',
    '$2y$10$sXx4NxMfGnkGLKoOsjJpneD2GmHeCRHh6j2/LoDdHv6lRqU/Ojlai',
    'lDAPKstN5QZRcNti8qJeSTNekmHHYy1ziIb6pAnGYvs=',
    'Tenant Farmer', 'Corn', NULL,
    'farmer', 1, 0, 0, NULL, '2026-11-19 08:30:00', '2026-11-19 08:30:00', '2026-11-19 08:30:00'
);

-- 2. SEED AUTHENTICATION QUESTIONS (Q1: birthplace, Q2: elementary, Q5: pet name)
INSERT INTO `authentication_answers` (`user_id`, `question_key`, `answer_hash`, `created_at`) VALUES
('2020-1050', 'Q1', '$2y$10$O5ic/B/dFAk5WG2FmkWoDegybWrhpwRyjCBSVRZi.FcmL.wQGxgd6', '2026-11-19 08:00:00'),
('2020-1050', 'Q2', '$2y$10$dP2izUWO02tbC67689WyOOutm071GiphMTaV5njCwAenxHDBqJO7i', '2026-11-19 08:00:00'),
('2020-1050', 'Q5', '$2y$10$2.LtI8szf.vPcbHD1TT9mOD6Nh3HFKBn6YRX530TmP5SHNtbE5X46', '2026-11-19 08:00:00'),
('2020-1051', 'Q1', '$2y$10$O5ic/B/dFAk5WG2FmkWoDegybWrhpwRyjCBSVRZi.FcmL.wQGxgd6', '2026-11-19 08:15:00'),
('2020-1051', 'Q2', '$2y$10$dP2izUWO02tbC67689WyOOutm071GiphMTaV5njCwAenxHDBqJO7i', '2026-11-19 08:15:00'),
('2020-1051', 'Q5', '$2y$10$2.LtI8szf.vPcbHD1TT9mOD6Nh3HFKBn6YRX530TmP5SHNtbE5X46', '2026-11-19 08:15:00'),
('2020-1052', 'Q1', '$2y$10$O5ic/B/dFAk5WG2FmkWoDegybWrhpwRyjCBSVRZi.FcmL.wQGxgd6', '2026-11-19 08:30:00'),
('2020-1052', 'Q2', '$2y$10$dP2izUWO02tbC67689WyOOutm071GiphMTaV5njCwAenxHDBqJO7i', '2026-11-19 08:30:00'),
('2020-1052', 'Q5', '$2y$10$2.LtI8szf.vPcbHD1TT9mOD6Nh3HFKBn6YRX530TmP5SHNtbE5X46', '2026-11-19 08:30:00');

-- 3. PASSWORD HISTORY SEED
INSERT INTO `password_history` (`user_id`, `password_hash`, `created_at`) VALUES
('2020-1050', '$2y$10$wYd22L6DS5tBxeI4sqZ2OuUv.LsfW1HEyqSRUrH.oXUEtvvxb.CgO', '2026-11-19 08:00:00'),
('2020-1051', '$2y$10$sXx4NxMfGnkGLKoOsjJpneD2GmHeCRHh6j2/LoDdHv6lRqU/Ojlai', '2026-11-19 08:15:00'),
('2020-1052', '$2y$10$sXx4NxMfGnkGLKoOsjJpneD2GmHeCRHh6j2/LoDdHv6lRqU/Ojlai', '2026-11-19 08:30:00');

-- 4. SEED ASSISTANCE REQUESTS
INSERT INTO `assistance_requests` (`farmer_id`, `program_category`, `preferred_date`, `farm_location`, `request_details`, `status`, `officer_notes`, `created_at`, `updated_at`) VALUES
('2020-1051', 'Fertilizer & Seed Subsidy', '2026-11-25', 'Purok 2 Green Valley, Los Angeles, Butuan City', 'Requesting 4 bags of Certified Inbred Rice Seeds and Urea fertilizer subsidy for upcoming wet cropping season.', 'Approved', 'Allocated 4 bags NSIC Rc222 and 2 bags fertilizer voucher under Municipal Agri Office.', '2026-11-19 08:45:00', '2026-11-19 09:10:00'),
('2020-1051', 'Soil & Agronomy Advisory', '2026-12-02', 'Purok 2 Green Valley, Los Angeles, Butuan City', 'Soil pH and nutrient deficiency test needed due to yellowing of lower rice leaves.', 'Pending', NULL, '2026-11-19 09:20:00', '2026-11-19 09:20:00'),
('2020-1052', 'Crop Insurance & Protection', '2026-11-28', 'Purok 5 Riverside, Baan, Butuan City', 'Enrollment for Philippine Crop Insurance Corporation (PCIC) coverage for 1.5 hectares hybrid corn farm.', 'Pending', NULL, '2026-11-19 09:35:00', '2026-11-19 09:35:00');

-- 5. INITIAL AUDIT LOGS
INSERT INTO `activity_logs` (`event_type`, `username_typed`, `user_id`, `ip_address`, `user_agent`, `details`, `created_at`) VALUES
('REGISTERED', 'admin_officer', '2020-1050', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'Initial agricultural administrator profile created.', '2026-11-19 08:00:00'),
('ACCOUNT_VERIFIED', 'admin_officer', '2020-1050', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'Administrator verified successfully.', '2026-11-19 08:00:00'),
('REGISTERED', 'farmer_juan', '2020-1051', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'New farmer account registered.', '2026-11-19 08:15:00'),
('ACCOUNT_VERIFIED', 'farmer_juan', '2020-1051', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'Farmer OTP verified successfully.', '2026-11-19 08:15:00'),
('REGISTERED', 'farmer_maria', '2020-1052', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'New farmer account registered.', '2026-11-19 08:30:00'),
('ACCOUNT_VERIFIED', 'farmer_maria', '2020-1052', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'Farmer OTP verified successfully.', '2026-11-19 08:30:00');
