<?php
/**
 * M-Pesa Callback URL Handler
 * This file receives callback data from M-Pesa after a transaction
 */

require 'db.php';
date_default_timezone_set('Africa/Nairobi');

// Get the raw POST data
$content = file_get_contents('php://input');
$data = json_decode($content, true);

// Log the callback data for debugging
$logFile = 'M_PESAConfirmationResponse.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . "\n" . print_r($data, true) . "\n\n", FILE_APPEND);

// Respond to M-Pesa immediately (required)
header('Content-Type: application/json');
$response = [
    'ResultCode' => 0,
    'ResultDesc' => 'Accept'
];
echo json_encode($response);

// Process the callback data
if (isset($data['Body']['stkCallback'])) {
    $callback = $data['Body']['stkCallback'];
    $merchantRequestID = $callback['MerchantRequestID'] ?? null;
    $checkoutRequestID = $callback['CheckoutRequestID'] ?? null;
    $resultCode = $callback['ResultCode'] ?? null;
    $resultDesc = $callback['ResultDesc'] ?? '';
    
    // If transaction was successful
    if ($resultCode == 0 && isset($callback['CallbackMetadata']['Item'])) {
        $items = $callback['CallbackMetadata']['Item'];
        $mpesaCode = '';
        $phone = '';
        $amount = 0;
        
        // Extract data from callback metadata
        foreach ($items as $item) {
            if ($item['Name'] == 'MpesaReceiptNumber') {
                $mpesaCode = $item['Value'];
            } elseif ($item['Name'] == 'PhoneNumber') {
                $phone = $item['Value'];
            } elseif ($item['Name'] == 'Amount') {
                $amount = $item['Value'];
            }
        }
        
        // Insert successful transaction into database
        try {
            $stmt = $pdo->prepare("
                INSERT INTO payments (phone, amount, mpesa_code, result_code, result_desc, merchant_request_id, checkout_request_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $phone,
                $amount,
                $mpesaCode,
                $resultCode,
                $resultDesc,
                $merchantRequestID,
                $checkoutRequestID
            ]);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
        }
    } else {
        // Transaction failed - still log it
        try {
            $stmt = $pdo->prepare("
                INSERT INTO payments (phone, amount, result_code, result_desc, merchant_request_id, checkout_request_id) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                '', // Phone not available in failed transactions
                0,  // Amount not available in failed transactions
                $resultCode,
                $resultDesc,
                $merchantRequestID,
                $checkoutRequestID
            ]);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
        }
    }
}
