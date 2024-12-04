<?php 

namespace Taitech\Azampay\Modules;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * The base class of everything
 */

 class AzampayModule
 {
    private const BASE_URL = "https://checkout.azampay.co.tz";

    private const AUTH_URL = "https://authenticator.azampay.co.tz";

    private const SANDBOX_BASE_URL = "https://sandbox.azampay.co.tz";

    private const SANDBOX_AUTH_URL = "https://authenticator-sandbox.azampay.co.tz";
 
     private string $app;
     private string $clientId;
     private string $secret;
     private string $env;
     protected Client $httpClient;
 
     public function __construct(string $app, string $clientId, string $secret, string $env = 'SANDBOX')
     {
         $this->app = $app;
         $this->clientId = $clientId;
         $this->secret = $secret;
         $this->env = strtoupper($env);
         $this->httpClient = new Client(['timeout' => 30, 'keep_alive' => true]);
     }
 
     /**
      * Generate a Token
      *
      * @return string
      * @throws Exception
      */
     public function generateToken(): string
     {
         $url = $this->getEnvironmentUrl('auth');
 
         try {
             $response = $this->httpClient->post($url . '/AppRegistration/GenerateToken', [
                 'headers' => ['Content-Type' => 'application/json'],
                 'json' => [
                     'appName' => $this->app,
                     'clientId' => $this->clientId,
                     'clientSecret' => $this->secret,
                 ],
             ]);
 
             $data = json_decode($response->getBody(), true);
 
             if (isset($data['data']['accessToken'])) {
                 return $data['data']['accessToken'];
             }
 
             throw new Exception('Token generation failed: Missing token in response.');
         } catch (RequestException $e) {
             throw new Exception('HTTP Request failed: ' . $e->getMessage());
         } catch (Exception $e) {
             throw new Exception('Token generation error: ' . $e->getMessage());
         }
     }




 
     /**
      * Get the appropriate environment URL
      *
      * @param string $type
      * @return string
      */
     protected function getEnvironmentUrl(string $type): string
     {
         if ($type === 'auth') {
             return $this->env === 'SANDBOX' ? self::SANDBOX_AUTH_URL : self::AUTH_URL;
         }
         return $this->env === 'SANDBOX' ? self::SANDBOX_BASE_URL : self::BASE_URL;
     }
 }
