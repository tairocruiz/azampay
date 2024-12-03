<?php

namespace Taitech\Azampay;

use Taitech\Azampay\Services\MNOServices;
use Taitech\Azampay\Services\BankServices;
use Taitech\Azampay\Services\MerchantServices;
use Exception;

final class AzampayService
{
    private $appName;
    private $clientId;
    private $secret;
    private $env;
    private $service;

    private $serviceInstance;

    /**
     * AzampayService constructor.
     *
     * @param string $appName
     * @param string $clientId
     * @param string $secret
     * @param string $env
     * @param string $service
     * @throws Exception
     */
    public function __construct(string $appName, string $clientId, string $secret, string $env = 'SANDBOX', string $service = 'MNO')
    {
        $this->appName = $appName;
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->env = $env;
        $this->service = strtoupper($service);

        // Initialize the appropriate service instance
        switch ($this->service) {
            case 'MNO':
                $this->serviceInstance = new MNOServices($this->appName, $this->clientId, $this->secret, $this->env);
                break;
            case 'BANK':
                $this->serviceInstance = new BankServices();
                break;
            case 'MERCHANT':
                $this->serviceInstance = new MerchantServices();
                break;
            default:
                throw new Exception("Invalid service type: {$this->service}. Supported services are MNO, BANK, and MERCHANT.");
        }
    }

    /**
     * Proxy method to call service-specific functionality.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call(string $method, array $arguments)
    {
        if (method_exists($this->serviceInstance, $method)) {
            // Inject credentials if the method requires it
            if (method_exists($this->serviceInstance, 'setCredentials')) {
                $mt = 'setCredentials';
                $this->serviceInstance->$mt($this->appName, $this->clientId, $this->secret, $this->env);
            }

            return call_user_func_array([$this->serviceInstance, $method], $arguments);
        }

        throw new Exception("Method {$method} does not exist on the {$this->service} service.");
    }
}
