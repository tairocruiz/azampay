<?php

namespace Taitech\Azampay;

use Taitech\Azampay\Services\MNOServices;
use Taitech\Azampay\Services\BankServices;
use Taitech\Azampay\Services\MerchantServices;
use Exception;

final class AzampayService
{
    private string $appName;
    private string $clientId;
    private string $secret;
    private string $env;
    private string $service;

    private object $serviceInstance;

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
        $this->env = strtoupper($env);
        $this->service = strtoupper($service);

        $this->initializeServiceInstance();
    }

    /**
     * Initialize the appropriate service instance lazily.
     *
     * @throws Exception
     */
    private function initializeServiceInstance(): void
    {
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
            // Skip unnecessary checks and directly call the method
            return $this->serviceInstance->$method(...$arguments);
        }

        throw new Exception("Method {$method} does not exist on the {$this->service} service.");
    }
}
