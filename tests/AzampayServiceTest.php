<?php

namespace Taitech\Azampay\Tests;

use PHPUnit\Framework\TestCase;
use Taitech\Azampay\AzampayService;
use Taitech\Azampay\Services\MNOServices;
use Exception;

class AzampayServiceTest extends TestCase
{
    private AzampayService $azampay;

    protected function setUp(): void
    {
        $this->azampay = new AzampayService(
            'test_app',
            'test_client_id',
            'test_secret',
            'SANDBOX',
            'MNO'
        );
    }

    public function test_service_initialization()
    {
        $this->assertInstanceOf(AzampayService::class, $this->azampay);
    }

    public function test_mno_service_creation()
    {
        // Test that MNO service is properly initialized
        $this->assertInstanceOf(MNOServices::class, $this->azampay);
    }

    public function test_invalid_service_type()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid service type: INVALID. Supported services are MNO, BANK, and MERCHANT.');
        
        new AzampayService(
            'test_app',
            'test_client_id',
            'test_secret',
            'SANDBOX',
            'INVALID'
        );
    }

    public function test_method_proxy()
    {
        // Test that methods are properly proxied to the service instance
        $this->assertTrue(method_exists($this->azampay, 'checkout'));
    }

    public function test_invalid_method_call()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Method invalidMethod does not exist on the MNO service.');
        
        $this->azampay->invalidMethod();
    }
}
