<?php
/**
 * Configuration Sample Template
 * IT 107 - Information Assurance and Security 1
 * Secure Farmers Registration, Assistance and Monitoring System
 * Student ID: 2020-1050
 */

// Prevent direct execution via browser
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    http_response_code(404);
    exit('Page Not Found.');
}

return [
    'db' => [
        'host'     => '127.0.0.1',
        'dbname'   => 'farmers_system_db',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],

    'smtp' => [
        'host'       => 'smtp.gmail.com',
        'port'       => 587,
        'auth'       => true,
        'secure'     => 'tls',
        'username'   => 'your_email@csucc.edu.ph',
        'password'   => 'your_gmail_app_password',
        'from_email' => 'your_email@csucc.edu.ph',
        'from_name'  => 'Secure Farmers Portal',
    ],

    'captcha' => [
        'site_key'   => 'YOUR_RECAPTCHA_V2_SITE_KEY',
        'secret_key' => 'YOUR_RECAPTCHA_V2_SECRET_KEY',
    ],

    'security' => [
        'aes_key' => '38k/xvQwxk12kHb/wtyJhh7QPNhfYVj4ZAqTdkk1lrk=',
    ],

    'seed' => [
        'min_username_len'       => 6,
        'max_username_len'       => 20,
        'min_password_len'       => 8,
        'max_name_len'           => 50,
        'forgot_pwd_threshold'   => 2,
        'lockout_tier_1_sec'     => 15,
        'lockout_tier_2_sec'     => 30,
        'lockout_tier_3_sec'     => 60,
        'otp_expiry_minutes'     => 5,
        'idle_session_minutes'   => 15,
        'ip_block_threshold'     => 10,
    ]
];
