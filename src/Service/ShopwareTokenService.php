<?php
namespace App\Service;

use App\Entity\Shop;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ShopwareTokenService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function fetchToken(Shop $shop): ?string
    {

        $url = rtrim($shop->getShopUrl(), '/') . '/api/oauth/token';
        $response = $this->httpClient->request('POST', $url, [
            'http_version' => '1.1',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => http_build_query([
                'grant_type' => 'client_credentials',
                'client_id' => $shop->getShopClientId(),
                'client_secret' => $shop->getShopClientSecret(),
            ]),
        ]);

        var_dump($response->getContent());

        if ($response->getStatusCode() !== 200) {
            error_log('Token-Request fehlgeschlagen: Status ' . $response->getStatusCode() . ' - Antwort: ' . $response->getContent(false));
            return null;
        }

        $data = $response->toArray();
        return $data['access_token'] ?? null;
    }
}
