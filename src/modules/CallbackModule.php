<?php

namespace Taitech\Azampay\Modules;

class CallbackModule
{
    // Your secret key for signature verification (provided by AzamPay)
    protected $secretKey; 

    public function __construct(string $secretKey){
        $this->secretKey = $secretKey;
    }

    // Main method for handling the callback
    public function callback()
    {
        // Get the raw POST data
        $request_body = file_get_contents('php://input');
        // Decode the JSON data into an associative array
        $data = json_decode($request_body, true);
        
        // Verify the HMAC signature
        if (!$this->isValidSignature($request_body)) {
            // If the signature is invalid, respond with 400
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid signature']);
            return;
        }

        // Validate required fields in the callback data
        $required_fields = ['transactionstatus', 'operator', 'reference', 'utilityref', 'amount', 'msisdn'];
        
        foreach ($required_fields as $field) {
            // Check if the required field is missing or empty
            if (!isset($data[$field]) || empty($data[$field])) {
                // Respond with an error and a 400 status code if any required field is missing or empty
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing or invalid field: {$field}"]);
                return;
            }
        }

        // Optional: Handle additionalProperties if present
        if (isset($data['additionalProperties']) && is_array($data['additionalProperties'])) {
            // Handle the additional properties here if needed
            $additionalProps = $data['additionalProperties'];
        }

        // Extract required data from the callback payload
        $transactionstatus = $data['transactionstatus'];
        $reference = $data['reference'];
        $amount = $data['amount'];

        // Log transaction details if necessary (commented out for now)
        // file_put_contents('callback_log.txt', json_encode($data) . PHP_EOL, FILE_APPEND);

        // Respond to AzamPay with a success or failure message based on transaction status
        if ($transactionstatus === 'success') {
            // If the transaction was successful, send a 200 OK response
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Callback received successfully']);
        } else {
            // If the transaction failed, send a 200 OK response with a failure message
            http_response_code(200);
            echo json_encode(['success' => false, 'message' => 'Transaction failed. Logged for review.']);
        }
    }

    // Method to verify the HMAC signature
    private function isValidSignature($request_body)
    {
        // Get the signature sent by AzamPay in the request headers
        $receivedSignature = $_SERVER['HTTP_X_SIGNATURE']; // Example header for signature
        
        // Calculate the HMAC hash of the request body using the secret key
        $calculatedSignature = hash_hmac('sha256', $request_body, $this->secretKey);
        
        // Compare the calculated signature with the received signature
        return hash_equals($receivedSignature, $calculatedSignature);
    }
}
