<?php
if (isset($_POST['submit'])) {
  require 'db.php';
  date_default_timezone_set('Africa/Nairobi');

  $consumerKey = 'hATBLugw84usb112idYMZpunehnusoUuwQ75CvuQUJ8OUmcO';
  $consumerSecret = 'mueHUo4XcEMVdbD5DuYjmaZc5GBf1jjFpKkyoFpNQtkL7rF7rhqhxwW9QAJgxgfp';
  $BusinessShortCode = '174379';
  $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

  $PartyA = $_POST['phone'];
  $AccountReference = 'Test123';
  $TransactionDesc = 'M-Pesa Payment';
  $Amount = $_POST['amount'];
  $Timestamp = date('YmdHis');
  $Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);

  $headers = ['Content-Type:application/json; charset=utf8'];
  $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

  $curl = curl_init($access_token_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
  $result = json_decode(curl_exec($curl));
  $access_token = $result->access_token;
  curl_close($curl);

  $stkheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
  $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
  $CallBackURL = 'https://your-app.onrender.com/callback_url.php';

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

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $initiate_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
  $response = curl_exec($curl);
  curl_close($curl);

  header("Location: status.php?status=waiting");
  exit();
}
