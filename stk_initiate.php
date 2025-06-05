<?php
session_start();
if (isset($_POST['submit'])) {
    date_default_timezone_set('Africa/Nairobi');

    // Database connection (adjust with your own credentials)
    $conn = new mysqli("localhost", "db_username", "db_password", "mpesa_db");
    if ($conn->connect_error) {
        die("DB Connection failed: " . $conn->connect_error);
    }

    $consumerKey = 'nk16Y74eSbTaGQgc9WF8j6FigApqOMWr';
    $consumerSecret = '40fD1vRXCq90XFaU';

    $BusinessShortCode = '174379';
    $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    $PartyA = $_POST['phone'];
    $AccountReference = '2255';
    $TransactionDesc = 'Test Payment';
    $Amount = $_POST['amount'];

    $Timestamp = date('YmdHis');
    $Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);

    $headers = ['Content-Type:application/json; charset=utf8'];
    $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $CallBackURL = 'https://morning-basin-87523.herokuapp.com/callback_url.php'; // Your actual callback URL here

    // Get access token
    $curl = curl_init($access_token_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
    $result = curl_exec($curl);
    $result = json_decode($result);
    $access_token = $result->access_token;
    curl_close($curl);

    // Prepare STK push
    $stkheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $initiate_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader);

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

    $data_string = json_encode($curl_post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    $curl_response = curl_exec($curl);
    curl_close($curl);

    $response = json_decode($curl_response, true);

    if (isset($response['ResponseCode']) && $response['ResponseCode'] == "0") {
        // Store initial transaction with status 'Pending'
        $checkoutRequestId = $response['CheckoutRequestID'];
        $merchantRequestId = $response['MerchantRequestID'];
        $stmt = $conn->prepare("INSERT INTO mpesa_transactions (checkout_request_id, merchant_request_id, amount, phone_number, status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("ssds", $checkoutRequestId, $merchantRequestId, $Amount, $PartyA);
        $stmt->execute();
        $stmt->close();

        $_SESSION['transaction_status'] = "STK Push request sent successfully. Check your phone to complete the payment.";
    } else {
        $_SESSION['transaction_status'] = "STK Push failed: " . ($response['errorMessage'] ?? 'Unknown error');
    }

    $conn->close();
    header("Location: status.php");
    exit;
}
?>
