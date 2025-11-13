<?php
namespace App\Service;

use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class ShopifyOrderService
{
    private LoggerInterface $logger;
    private Client $guzzleClient;

    const SHOPIFY_API_URL = "https://dev-sp-iff-05.myshopify.com/admin/api/2025-10/graphql.json";
    const SHOPIFY_ACCESS_TOKEN = "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";

    public function __construct(LoggerInterface $logger, Client $guzzleClient)
    {
        $this->logger = $logger;
        $this->guzzleClient = $guzzleClient;
    }

    public function importOrder(?string $orderId = null): void  {

        $request = new Request(
            'POST',
            self::SHOPIFY_API_URL,
            [
                'X-Shopify-Access-Token'    => self::SHOPIFY_ACCESS_TOKEN,
                'Content-Type'              => 'application/json',
            ],
            '{"query": "query { order(id: \"' . $orderId . '\") { id name totalPriceSet { presentmentMoney { amount } } lineItems(first: 10) { nodes { id name } } } }"}'
        );

        $response = $this->guzzleClient->send($request);
        $data = json_decode($response->getBody()->getContents(), true);


        dump($data['data']['order']);

        die;
    }
}
