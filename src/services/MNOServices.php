<?php

namespace Taitech\Azampay\Services;

use Exception;
use GuzzleHttp\Exception\RequestException;
use Taitech\Azampay\Helpers\MNOPayloadValidator;
use Taitech\Azampay\Modules\AzampayModule;


class MNOServices extends AzampayModule
{
    use MNOPayloadValidator;

    /**
     * Perform a checkout operation
     *
     * @param array $payload
     * @return array
     * @throws Exception
     */
    public function checkout(array $payload): array
    {
        $this->preparePayload($payload);
        $this->validatePayload($payload);

        $url = $this->getEnvironmentUrl('base') . '/azampay/mno/checkout';
        $token = $this->generateToken();

        try {
            $response = $this->httpClient->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'json' => $payload,
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new Exception('Checkout failed: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Unexpected error during checkout: ' . $e->getMessage());
        }
    }

    /**
     * Prepare the payload with required additional fields
     *
     * @param array $payload
     */
    private function preparePayload(array &$payload): void
    {
        $payload['externalId'] = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        $payload['currency'] = 'TZS';
    }
}