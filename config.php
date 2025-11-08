<?php
/**
 * M-Pesa Configuration
 * Update these settings according to your environment
 */

// M-Pesa API Credentials
// Reads from environment variables (Render) or uses defaults (local development)
define('MPESA_CONSUMER_KEY', $_ENV['MPESA_CONSUMER_KEY'] ?? 'hATBLugw84usb112idYMZpunehnusoUuwQ75CvuQUJ8OUmcO');
define('MPESA_CONSUMER_SECRET', $_ENV['MPESA_CONSUMER_SECRET'] ?? 'mueHUo4XcEMVdbD5DuYjmaZc5GBf1jjFpKkyoFpNQtkL7rF7rhqhxwW9QAJgxgfp');
define('MPESA_BUSINESS_SHORTCODE', $_ENV['MPESA_BUSINESS_SHORTCODE'] ?? '174379');
define('MPESA_PASSKEY', $_ENV['MPESA_PASSKEY'] ?? 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');

// Environment: 'sandbox' or 'production'
// Can be overridden with MPESA_ENVIRONMENT environment variable
define('MPESA_ENVIRONMENT', $_ENV['MPESA_ENVIRONMENT'] ?? 'sandbox');

// API URLs
if (MPESA_ENVIRONMENT === 'sandbox') {
    define('MPESA_ACCESS_TOKEN_URL', 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    define('MPESA_STK_PUSH_URL', 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
} else {
    define('MPESA_ACCESS_TOKEN_URL', 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    define('MPESA_STK_PUSH_URL', 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
}

// Callback URL Configuration
// IMPORTANT: For M-Pesa sandbox, this MUST be a publicly accessible URL
// 
// For Render deployment:
// - Leave empty to auto-detect from environment (recommended)
// - Or set manually: define('MPESA_CALLBACK_URL', 'https://your-app.onrender.com/callback_url.php');
//
// For local development, you'll need to deploy to Render first
// Render provides a permanent HTTPS URL for your application

// Set this to your public callback URL, or leave empty to auto-detect from Render environment
define('MPESA_CALLBACK_URL', '');

// Account Reference and Transaction Description
define('MPESA_ACCOUNT_REFERENCE', 'Test123');
define('MPESA_TRANSACTION_DESC', 'M-Pesa Payment');

