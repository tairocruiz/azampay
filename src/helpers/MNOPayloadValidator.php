<?php

namespace Taitech\Azampay\Helpers;

use Exception;

/**
 * 
 * @method bool validatePayload(array $payload);
 * @method string sanitizeAccountNumber(string $accountNumber);
 * @method array castPayload(array $payload);
 * 
 */

trait MNOPayloadValidator {
    public function validatePayload(array $payload) {
        $requiredFields = [
            'amount',
            'accountNumber',
            'provider',
        ];
        // Check for missing fields
        $missingFields = array_diff($requiredFields, array_keys($payload));

        if (!empty($missingFields)) {
            throw new Exception(json_encode([
                'error' => 'Validation Error',
                'message' => 'Missing required fields',
                'missingFields' => $missingFields,
            ]));
        }

        return true;
    }

     public function sanitizeAccountNumber($accountNumber)
    {
        // Remove any non-digit characters from the account number
        $mobileNumber = preg_replace('/[^0-9]/', '', $accountNumber);
        if (strlen($mobileNumber) < 9 || strlen($mobileNumber) > 12) {
            throw new \Exception('Invalid mobile number');
        }

        if (strlen($mobileNumber) == 9 && $mobileNumber[0] != '0') {
            $mobileNumber = "255{$mobileNumber}";
        } elseif (strlen($mobileNumber) == 10 && $mobileNumber[0] == '0') {
            $mobileNumber = str_replace('0', '255', $mobileNumber, 1);
        }

        return $mobileNumber;
    }

    /**
     * Cast and validate the payload.
     *
     * @param array $payload
     * @return array
     */
    public function castPayload(array $payload)
    {
        try {
            // Validate the payload
            $this->validatePayload($payload);

            // Sanitize and cast fields
            $payload['amount'] = $payload['amount'];
            $payload['accountNumber'] = $this->sanitizeAccountNumber($payload['accountNumber']);
            $payload['provider'] = (string) $payload['provider'];
            $payload['externalId'] = uniqid('mbora_');
            $payload['currency'] = 'TZS';
            $payload['additionalProperties'] = $payload['additionalProperties'];

            return $payload;
        } catch (Exception $e) {
            // Handle exceptions and return as JSON response
            return json_encode([
                'success' => false,
                'error' => 'Payload Casting Error',
                'message' => $e->getMessage(),
            ]);
        }
    }

}