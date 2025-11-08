<?php
/**
 * Test STK Push
 * Initiates an STK push to a specific phone number
 */

require 'db.php';
date_default_timezone_set('Africa/Nairobi');

// Phone number to test
$phoneNumber = '+254796913123';
$amount = 1; // Test amount in KES

echo "========================================\n";
echo "M-Pesa STK Push Test\n";
echo "========================================\n\n";

echo "Phone Number: $phoneNumber\n";
echo "Amount: $amount KES\n\n";

// Clean phone number (remove + and non-digits)
$PartyA = preg_replace('/[^0-9]/', '', $phoneNumber);

// Ensure phone number starts with 254
if (!str_starts_with($PartyA, '254')) {
    if (str_starts_with($PartyA, '0')) {
        $PartyA = '254' . substr($PartyA, 1);
    } else {
        $PartyA = '254' . $PartyA;
    }
}

echo "Formatted Phone: $PartyA\n";
echo "Testing connection...\n";
echo str_repeat('-', 40) . "\n\n";

// Load configuration
if (file_exists('config.php')) {
    require 'config.php';
    $consumerKey = MPESA_CONSUMER_KEY;
    $consumerSecret = MPESA_CONSUMER_SECRET;
    $BusinessShortCode = MPESA_BUSINESS_SHORTCODE;
    $Passkey = MPESA_PASSKEY;
    $AccountReference = MPESA_ACCOUNT_REFERENCE;
    $TransactionDesc = MPESA_TRANSACTION_DESC;
    $access_token_url = MPESA_ACCESS_TOKEN_URL;
    $initiate_url = MPESA_STK_PUSH_URL;
} else {
    // Fallback to hardcoded values
    $consumerKey = 'hATBLugw84usb112idYMZpunehnusoUuwQ75CvuQUJ8OUmcO';
    $consumerSecret = 'mueHUo4XcEMVdbD5DuYjmaZc5GBf1jjFpKkyoFpNQtkL7rF7rhqhxwW9QAJgxgfp';
    $BusinessShortCode = '174379';
    $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    $AccountReference = 'Test123';
    $TransactionDesc = 'M-Pesa Payment Test';
    $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
}

$Amount = floatval($amount);
$Timestamp = date('YmdHis');
$Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);

echo "Step 1: Getting access token...\n";
// Get access token
$headers = ['Content-Type:application/json; charset=utf8'];

$curl = curl_init($access_token_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
$result = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curlError = curl_error($curl);
curl_close($curl);

if ($httpCode != 200) {
    echo "✗ Failed to get access token\n";
    echo "HTTP Code: $httpCode\n";
    if ($curlError) {
        echo "cURL Error: $curlError\n";
    }
    echo "Response: $result\n";
    exit(1);
}

$result = json_decode($result);
if (!isset($result->access_token)) {
    echo "✗ Invalid access token response\n";
    print_r($result);
    exit(1);
}

$access_token = $result->access_token;
echo "✓ Access token obtained\n\n";

// Determine callback URL
if (defined('MPESA_CALLBACK_URL') && !empty(MPESA_CALLBACK_URL)) {
    $CallBackURL = MPESA_CALLBACK_URL;
} else {
    // Try to auto-detect, but this won't work for localhost
    $protocol = 'http://';
    $host = 'localhost:8000';
    $CallBackURL = $protocol . $host . '/callback_url.php';
    
    echo "⚠️  WARNING: No callback URL configured!\n";
    echo "   M-Pesa requires a publicly accessible callback URL.\n\n";
    echo "   Please deploy to Render and set MPESA_CALLBACK_URL in config.php\n";
    echo "   Or set it manually: define('MPESA_CALLBACK_URL', 'https://your-app.onrender.com/callback_url.php');\n\n";
    echo "   See RENDER_DEPLOYMENT.md for deployment instructions.\n\n";
    echo "   This test will fail without a valid callback URL.\n\n";
}

echo "Step 2: Initiating STK Push...\n";
echo "Callback URL: $CallBackURL\n";

// Prepare STK Push request
$stkheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];

$curl_post_data = [
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $Password,
    'Timestamp' => $Timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $Amount,
    'PartyA' => $PartyA,
    'PartyB' => $BusinessShortCode,
    'PhoneNumber' => $PartyA,
    'CallBackURL' => $CallBackURL,
    'AccountReference' => $AccountReference,
    'TransactionDesc' => $TransactionDesc
];

echo "Request Data:\n";
echo json_encode($curl_post_data, JSON_PRETTY_PRINT) . "\n\n";

// Send STK Push request
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $initiate_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curlError = curl_error($curl);
curl_close($curl);

$responseData = json_decode($response, true);

echo "Step 3: Processing response...\n";
echo "HTTP Code: $httpCode\n";
if ($curlError) {
    echo "cURL Error: $curlError\n";
}
echo "\nResponse Data:\n";
echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n\n";

// Save payment request to database
try {
    $merchantRequestID = $responseData['MerchantRequestID'] ?? null;
    $checkoutRequestID = $responseData['CheckoutRequestID'] ?? null;
    $responseCode = $responseData['ResponseCode'] ?? null;
    $responseDescription = $responseData['ResponseDescription'] ?? '';

    $stmt = $pdo->prepare("
      INSERT INTO payments (phone, amount, result_code, result_desc, merchant_request_id, checkout_request_id) 
      VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
      $PartyA,
      $Amount,
      $responseCode,
      $responseDescription,
      $merchantRequestID,
      $checkoutRequestID
    ]);
    
    echo "Step 4: Saving to database...\n";
    echo "✓ Payment request saved to database\n";
    echo "  - Merchant Request ID: $merchantRequestID\n";
    echo "  - Checkout Request ID: $checkoutRequestID\n\n";
    
} catch (PDOException $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n\n";
}

// Check if STK Push was initiated successfully
echo str_repeat('=', 40) . "\n";
if ($httpCode == 200 && isset($responseData['ResponseCode']) && $responseData['ResponseCode'] == '0') {
    echo "✓ STK PUSH INITIATED SUCCESSFULLY!\n";
    echo str_repeat('=', 40) . "\n\n";
    echo "Status: " . ($responseData['ResponseDescription'] ?? 'Success') . "\n";
    echo "Customer Message: " . ($responseData['CustomerMessage'] ?? 'Check your phone') . "\n\n";
    echo "📱 Please check phone number $PartyA for the M-Pesa prompt.\n";
    echo "   Enter your M-Pesa PIN to complete the transaction.\n\n";
    echo "The callback will be received at: $CallBackURL\n";
    echo "Check the database for transaction updates.\n";
} else {
    echo "✗ STK PUSH FAILED\n";
    echo str_repeat('=', 40) . "\n\n";
    $errorMsg = $responseData['errorMessage'] ?? $responseData['ResponseDescription'] ?? 'Unknown error';
    echo "Error: $errorMsg\n";
    if (isset($responseData['ResponseCode'])) {
        echo "Response Code: " . $responseData['ResponseCode'] . "\n";
    }
}

