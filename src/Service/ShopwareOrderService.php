<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Service\ShopwareTokenService;
use App\Entity\Shop;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;
use App\Entity\OrderImport;

class ShopwareOrderService
{
    private EntityManagerInterface $em;
    private ShopwareTokenService $tokenService;
    private LoggerInterface $logger;
    private Client $guzzleClient;

    public function __construct(EntityManagerInterface $em, ShopwareTokenService $tokenService, LoggerInterface $logger, Client $guzzleClient)
    {
        $this->em = $em;
        $this->tokenService = $tokenService;
        $this->logger = $logger;
        $this->guzzleClient = $guzzleClient;
    }

    public function importOrder(string $orderId, Shop $shop): ?OrderImport
    {
        $token = $this->tokenService->fetchToken($shop);
        $this->logger->info('Token-Antwort', ['token' => $token]);
        if (!$token) {
            $this->logger->error('Kein Token erhalten');
            return null;
        }
        $url = rtrim($shop->getShopUrl(), '/') . '/api/order/' . $orderId;
        try {
            $response = $this->guzzleClient->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'http_errors' => false,
            ]);
            $body = $response->getBody()->getContents();

            $this->logger->info('Bestell-API-Antwort', ['status' => $response->getStatusCode(), 'content' => $body]);
            if ($response->getStatusCode() !== 200) {
                $this->logger->error('Bestell-API-Fehler', ['status' => $response->getStatusCode(), 'content' => $body]);
                return null;
            }
            $orderData = json_decode($body, true);

            $orderImport = new OrderImport();
            $orderImport->setOrderId($orderId);
            $orderImport->setOrderNumber($orderData['data']['orderNumber'] ?? '');
            $orderImport->setOrderTotal($orderData['data']['amountTotal'] ?? 0.0);
            $this->em->persist($orderImport);
            $this->em->flush();
            return $orderImport;
        } catch (\Exception $e) {
            $this->logger->error('Bestell-API-Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
