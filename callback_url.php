<?php
header("Content-Type: application/json");

// Database connection (adjust with your credentials)
$conn = new mysqli("localhost", "db_username", "db_password", "mpesa_db");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["ResultCode" => 1, "ResultDesc" => "DB connection failed"]);
    exit;
}

$mpesaResponse = file_get_contents('php://input');
$data = json_decode($mpesaResponse, true);

// Log raw response (optional)
file_put_contents("M_PESAConfirmationResponse.txt", $mpesaResponse . PHP_EOL, FILE_APPEND);

if (isset($data['Body']['stkCallback'])) {
    $callback = $data['Body']['stkCallback'];

    $merchantRequestID = $callback['MerchantRequestID'] ?? '';
    $checkoutRequestID = $callback['CheckoutRequestID'] ?? '';
    $resultCode = $callback['ResultCode'] ?? -1;
    $resultDesc = $callback['ResultDesc'] ?? '';

    $receiptNumber = null;

    if ($resultCode == 0) {
        // Success: parse callback metadata for MpesaReceiptNumber
        $callbackMetadata = $callback['CallbackMetadata']['Item'] ?? [];
        foreach ($callbackMetadata as $item) {
            if ($item['Name'] == 'MpesaReceiptNumber') {
                $receiptNumber = $item['Value'];
            }
        }
        $status = 'Success';
    } else {
        $status = 'Failed';
    }

    // Update database record with transaction result
    $stmt = $conn->prepare("UPDATE mpesa_transactions SET result_code=?, result_desc=?, mpesa_receipt_number=?, status=? WHERE checkout_request_id=?");
    $stmt->bind_param("issss", $resultCode, $resultDesc, $receiptNumber, $status, $checkoutRequestID);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

// Send response to Safaricom
echo json_encode([
    "ResultCode" => 0,
    "ResultDesc" => "Confirmation Received Successfully"
]);
?>
