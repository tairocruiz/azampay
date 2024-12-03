<?php

namespace Taitech\Azampay\Services;

use GuzzleHttp\Exception\RequestException;
use Taitech\Azampay\Helpers\MNOPayloadValidator;
use Taitech\Azampay\Modules\AzampayModule;


class MNOServices extends AzampayModule {

    use MNOPayloadValidator;

    public function __construct(string $appName, string $clientId, string $clientSecret)
    {
        parent::__construct($appName, $clientId, $clientSecret);
    }

    public function checkout(array $payload, $env = 'SANDBOX') {

        $authToken = $this->generateToken($env);
        
        try {
            // Send a POST request to Azampay's checkout endpoint
           
            $response = $this->client->post(
                $this->getEnvironmentBaseUrl($env) . '/azampay/mno/checkout/', // Replace with actual endpoint
                [
                    'json' => $this->castPayload($payload), // Send data as JSON
                    'headers' => [
                        'Authorization' => 'Bearer ' . $authToken,
                        'Content-Type' => 'application/json',
                    ],
                    'timeout' => 30,
                ]
            );
    
            // If successful, get the response body
            $responseBody = $response->getBody()->getContents();
            
            // Return the raw response
            return json_encode([
                'success' => true,
                'rawResponse' => $responseBody,
            ]);
        } catch (RequestException $e) {
            // Handle request exceptions like connection issues, timeouts, etc.
            return json_encode([
                'success' => false,
                'message' => 'Request failed.',
                'errorDetails' => $e->getMessage(),
                'apiResponse' => null,
            ]);
        } catch (\Exception $e) {
            // Catch any other exceptions
            return json_encode([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'errorDetails' => $e->getMessage(),
                'apiResponse' => null,
            ]);
        }
    }
}