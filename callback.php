<?php
// callback.php - Robust M-Pesa Daraja Callback Handler

// 1. Set up logging
$logFile = 'mpesa_callback.log';
file_put_contents($logFile, "\n" . date('Y-m-d H:i:s') . " - New callback received\n", FILE_APPEND);

// 2. Get and log raw input
$rawData = file_get_contents('php://input');
file_put_contents($logFile, "Raw JSON: " . $rawData . "\n", FILE_APPEND);

// 3. Validate and decode JSON
$callbackData = json_decode($rawData);

if (json_last_error() !== JSON_ERROR_NONE) {
    $error = 'Invalid JSON: ' . json_last_error_msg();
    file_put_contents($logFile, $error . "\n", FILE_APPEND);
    respondWithError($error);
    exit;
}

// 4. Validate callback structure
if (!isset($callbackData->Body->stkCallback)) {
    $error = 'Missing stkCallback in response';
    file_put_contents($logFile, $error . "\n", FILE_APPEND);
    respondWithError($error);
    exit;
}

// 5. Extract main callback data
$callback = $callbackData->Body->stkCallback;
$merchantRequestID = $callback->MerchantRequestID ?? null;
$checkoutRequestID = $callback->CheckoutRequestID ?? null;
$resultCode = $callback->ResultCode ?? null;
$resultDesc = $callback->ResultDesc ?? null;

// Log basic info
file_put_contents($logFile, "MerchantRequestID: $merchantRequestID\n", FILE_APPEND);
file_put_contents($logFile, "CheckoutRequestID: $checkoutRequestID\n", FILE_APPEND);
file_put_contents($logFile, "ResultCode: $resultCode\n", FILE_APPEND);
file_put_contents($logFile, "ResultDesc: $resultDesc\n", FILE_APPEND);

// 6. Process successful payment
if ($resultCode == 0) {
    if (!isset($callback->CallbackMetadata->Item)) {
        $error = 'Missing CallbackMetadata for successful payment';
        file_put_contents($logFile, $error . "\n", FILE_APPEND);
        respondWithError($error);
        exit;
    }

    // Initialize variables
    $paymentData = [
        'amount' => null,
        'receipt_number' => null,
        'transaction_date' => null,
        'phone_number' => null
    ];

    // Extract metadata items
    foreach ($callback->CallbackMetadata->Item as $item) {
        switch ($item->Name) {
            case 'Amount':
                $paymentData['amount'] = $item->Value;
                break;
            case 'MpesaReceiptNumber':
                $paymentData['receipt_number'] = $item->Value;
                break;
            case 'TransactionDate':
                $paymentData['transaction_date'] = $item->Value;
                break;
            case 'PhoneNumber':
                $paymentData['phone_number'] = $item->Value;
                break;
        }
    }

    // Validate required fields
    if (in_array(null, $paymentData, true)) {
        $error = 'Missing required payment data';
        file_put_contents($logFile, $error . "\n", FILE_APPEND);
        respondWithError($error);
        exit;
    }

    // Format transaction date (convert from YYYYMMDDHHmmss to datetime)
    $transactionDate = DateTime::createFromFormat(
        'YmdHis',
        $paymentData['transaction_date']
    )->format('Y-m-d H:i:s');

    // Log extracted data
    file_put_contents($logFile, "Processed payment data:\n" . print_r($paymentData, true) . "\n", FILE_APPEND);
    file_put_contents($logFile, "Formatted transaction date: $transactionDate\n", FILE_APPEND);

    // 7. Process the payment (database, business logic, etc.)
    try {
        // Example: Save to database
        $dbResult = saveTransactionToDatabase(
            $merchantRequestID,
            $checkoutRequestID,
            $paymentData['amount'],
            $paymentData['receipt_number'],
            $paymentData['phone_number'],
            $transactionDate
        );

        if ($dbResult) {
            file_put_contents($logFile, "Transaction saved successfully\n", FILE_APPEND);
            
            // Example: Send confirmation SMS
            sendConfirmationSMS(
                $paymentData['phone_number'],
                $paymentData['amount'],
                $paymentData['receipt_number']
            );
        } else {
            file_put_contents($logFile, "Failed to save transaction\n", FILE_APPEND);
        }
    } catch (Exception $e) {
        file_put_contents($logFile, "Error processing transaction: " . $e->getMessage() . "\n", FILE_APPEND);
    }
} else {
    // Handle failed payment
    file_put_contents($logFile, "Payment failed: $resultDesc\n", FILE_APPEND);
    handleFailedPayment($merchantRequestID, $checkoutRequestID, $resultCode, $resultDesc);
}

// 8. Always respond successfully to M-Pesa
header('Content-Type: application/json');
echo json_encode([
    'ResultCode' => 0,
    'ResultDesc' => 'Callback processed successfully'
]);

// Helper Functions

function respondWithError($message) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode([
        'ResultCode' => 1,
        'ResultDesc' => $message
    ]);
    exit;
}

function saveTransactionToDatabase($merchantId, $checkoutId, $amount, $receipt, $phone, $date) {
    // Implement your database logic here
    // Example using PDO:
    /*
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=mpesa', 'user', 'pass');
        $stmt = $pdo->prepare("INSERT INTO transactions 
                              (merchant_request_id, checkout_request_id, amount, 
                               receipt_number, phone_number, transaction_date, status) 
                              VALUES (?, ?, ?, ?, ?, ?, 'completed')");
        return $stmt->execute([$merchantId, $checkoutId, $amount, $receipt, $phone, $date]);
    } catch (PDOException $e) {
        // Log or handle error
        return false;
    }
    */
    return true; // Remove this when implementing actual DB logic
}

function sendConfirmationSMS($phone, $amount, $receipt) {
    // Implement your SMS gateway integration
    // Example:
    /*
    $message = "Thank you! Payment of KES $amount received. Receipt: $receipt";
    // Use your SMS API here
    */
    file_put_contents($GLOBALS['logFile'], "SMS would be sent to $phone: Amount $amount, Receipt $receipt\n", FILE_APPEND);
}

function handleFailedPayment($merchantId, $checkoutId, $code, $description) {
    // Implement your failed payment handling
    // Example: Update database, notify admin, etc.
    file_put_contents($GLOBALS['logFile'], 
        "Failed payment recorded: MerchantID $merchantId, CheckoutID $checkoutId, Reason: $description\n", 
        FILE_APPEND);
}
?>