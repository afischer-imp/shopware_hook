<?php
namespace App\Service;

use App\Entity\Shop;
use GuzzleHttp\Client;

class ShopwareTokenService
{
    private Client $guzzleClient;

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    public function fetchToken(Shop $shop): ?string
    {
        $credentials = [
            'grant_type'    => 'client_credentials',
            'client_id'     => $shop->getShopClientId(),
            'client_secret' => $shop->getShopClientSecret(),
        ];
        $url = rtrim($shop->getShopUrl(), '/') . '/api/oauth/token';
        try {
            $response = $this->guzzleClient->post($url, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'form_params' => $credentials,
                'http_errors' => false,
            ]);
            $body = $response->getBody()->getContents();

            $data = json_decode($body, true);
            if ($response->getStatusCode() !== 200) {
                error_log('Token-Request fehlgeschlagen: Status ' . $response->getStatusCode() . ' - Antwort: ' . $body);
                return null;
            }
            return $data['access_token'] ?? null;
        } catch (\Exception $e) {
            error_log('Token-Request Exception: ' . $e->getMessage());
            return null;
        }
    }
}
