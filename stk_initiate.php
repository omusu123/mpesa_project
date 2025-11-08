<?php
/**
 * STK Push Initiation
 * Handles M-Pesa STK Push payment requests
 */

if (isset($_POST['submit'])) {
  require 'db.php';
  date_default_timezone_set('Africa/Nairobi');

  // M-Pesa API Credentials
  $consumerKey = 'hATBLugw84usb112idYMZpunehnusoUuwQ75CvuQUJ8OUmcO';
  $consumerSecret = 'mueHUo4XcEMVdbD5DuYjmaZc5GBf1jjFpKkyoFpNQtkL7rF7rhqhxwW9QAJgxgfp';
  $BusinessShortCode = '174379';
  $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

  // Get form data
  $PartyA = preg_replace('/[^0-9]/', '', $_POST['phone']); // Clean phone number
  $AccountReference = 'Test123';
  $TransactionDesc = 'M-Pesa Payment';
  $Amount = floatval($_POST['amount']);
  
  // Validate inputs
  if (empty($PartyA) || empty($Amount) || $Amount <= 0) {
    header("Location: index.php?error=Invalid phone number or amount");
    exit();
  }

  // Ensure phone number starts with 254 (Kenya country code)
  if (strlen($PartyA) == 9 && substr($PartyA, 0, 1) == '0') {
    $PartyA = '254' . substr($PartyA, 1);
  } elseif (strlen($PartyA) == 10 && substr($PartyA, 0, 1) == '0') {
    $PartyA = '254' . substr($PartyA, 1);
  }

  $Timestamp = date('YmdHis');
  $Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);

  // Get access token
  $headers = ['Content-Type:application/json; charset=utf8'];
  $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

  $curl = curl_init($access_token_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
  $result = curl_exec($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);

  if ($httpCode != 200) {
    header("Location: index.php?error=Failed to get access token. Please check your API credentials.");
    exit();
  }

  $result = json_decode($result);
  if (!isset($result->access_token)) {
    header("Location: index.php?error=Invalid API credentials. Please check your consumer key and secret.");
    exit();
  }
  
  $access_token = $result->access_token;

  // Determine callback URL
  // Priority: 1. Config file, 2. Environment variable (Render), 3. Auto-detect
  if (defined('MPESA_CALLBACK_URL') && !empty(MPESA_CALLBACK_URL)) {
    $CallBackURL = MPESA_CALLBACK_URL;
  } elseif (!empty($_ENV['RENDER_EXTERNAL_URL'])) {
    // Auto-detect from Render environment
    $CallBackURL = $_ENV['RENDER_EXTERNAL_URL'] . '/callback_url.php';
  } else {
    // Fallback to auto-detect (works on Render and most hosting)
    $protocol = (!empty($_SERVER['HTTPS']) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $CallBackURL = $protocol . $host . $scriptPath . '/callback_url.php';
  }

  // Prepare STK Push request
  $stkheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
  $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

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

  // Send STK Push request
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $initiate_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
  $response = curl_exec($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);

  $responseData = json_decode($response, true);

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
  } catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
  }

  // Check if STK Push was initiated successfully
  if ($httpCode == 200 && isset($responseData['ResponseCode']) && $responseData['ResponseCode'] == '0') {
    header("Location: status.php?status=waiting&message=" . urlencode($responseData['CustomerMessage'] ?? 'Please check your phone'));
  } else {
    $errorMsg = $responseData['errorMessage'] ?? $responseData['ResponseDescription'] ?? 'Failed to initiate payment';
    header("Location: index.php?error=" . urlencode($errorMsg));
  }
  exit();
} else {
  header("Location: index.php");
  exit();
}
