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

 trait MNOPayloadValidator
 {
     /**
      * Validate the payload structure
      *
      * @param array $payload
      * @throws Exception
      */
     public function validatePayload(array $payload): void
     {
         $requiredFields = ['amount', 'accountNumber', 'provider'];
 
         foreach ($requiredFields as $field) {
             if (empty($payload[$field])) {
                 throw new Exception("Validation Error: Missing required field '{$field}'.");
             }
         }
 
         $payload['accountNumber'] = $this->sanitizeAccountNumber($payload['accountNumber']);
     }
 
     /**
      * Sanitize the account number
      *
      * @param string $accountNumber
      * @return string
      */
     public function sanitizeAccountNumber(string $accountNumber): string
     {
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
 }