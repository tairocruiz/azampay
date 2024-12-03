<?php 

namespace Taitech\Azampay\Modules;

use Exception;
use GuzzleHttp\Client;

/**
 * The base class of everything
 */

class AzampayModule
{

    protected const BASE_URL = "https://checkout.azampay.co.tz";

    protected const AUTH_URL = "https://authenticator.azampay.co.tz";

    protected const SANDBOX_BASE_URL = "https://sandbox.azampay.co.tz";

    protected const SANDBOX_AUTH_URL = "https://authenticator-sandbox.azampay.co.tz";

    protected $appName;
    protected $clientId;
    protected $clientSecret;
    protected $env;
    protected $client;

    public function __construct(string $appName, string $clientId, string $clientSecret)
    {
        $this->appName = $appName;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->client = new Client();
    }

   
    public function generateToken($env = 'SANDBOX'){
        
        try {
            $response = $this->client->post($this->getEnvironmentUrl($env) . '/AppRegistration/GenerateToken', [
                'json' => [
                    'appName' => $this->appName,
                    'clientId' => $this->clientId,
                    'clientSecret' => $this->clientSecret,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            // return $data;
            
            if (isset($data['data']['accessToken'])) {
                return $data['data']['accessToken'];
            } else {
                throw new Exception('Unable to retrieve authentication token.');
            }
        } catch (Exception $e) {
            throw new Exception('Error retrieving authentication token: ' . $e->getMessage());
        }
    }

    public function getEnvironmentBaseUrl($env)
    {
        return strtolower($env) === 'production' || strtolower($env) === 'live'
            ? self::BASE_URL
            : self::SANDBOX_BASE_URL;
    }

    public function getEnvironmentUrl($env)
    {
        return strtolower($env) === 'production' || strtolower($env) === 'live'
            ? self::AUTH_URL
            : self::SANDBOX_AUTH_URL;
    }

    

    public function tokenGeneratorWithCURL(string $appName, string $clientId, string $secret, $env = 'SANDBOX'){
        $url = '';
        if ($env == 'PRODUCTION' || $env == 'production' || $env == 'live' || $env == 'LIVE') {
           $url = self::AUTH_URL;
        } else {
            $url = self::SANDBOX_AUTH_URL;
        }
        
        try {
            $data = [
                'appName' => $appName,
                'clientId' => $clientId,
                'clientSecret' => $secret,
            ];
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '/AppRegistration/GenerateToken');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
            $response = curl_exec($ch);
            curl_close($ch);
        
            $result = json_decode($response, true);
            if (isset($result['data']['accessToken'])) {
                return $result['data']['accessToken'];
            } else {
                throw new Exception('Unable to retrieve authentication token.');
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to retrieve authentication token.',
                    'details' => $result,
                ]);
                exit;
            }
        } catch (Exception $e) {
            throw new Exception('Error retrieving authentication token: ' . $e->getMessage());
        }
    }

    
}
