<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\OrderImport;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ShopwareTokenService;
use App\Entity\Shop;
use Psr\Log\LoggerInterface;

class ShopwareOrderService
{
    private HttpClientInterface $httpClient;
    private EntityManagerInterface $em;
    private ShopwareTokenService $tokenService;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $em, ShopwareTokenService $tokenService, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->em = $em;
        $this->tokenService = $tokenService;
        $this->logger = $logger;
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
        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
        $this->logger->info('Bestell-API-Antwort', ['status' => $response->getStatusCode(), 'content' => $response->getContent(false)]);
        if ($response->getStatusCode() !== 200) {
            $this->logger->error('Bestell-API-Fehler', ['status' => $response->getStatusCode(), 'content' => $response->getContent(false)]);
            return null;
        }
        $orderData = $response->toArray();
        $orderImport = new OrderImport();
        $orderImport->setOrderId($orderId);
        $orderImport->setOrderNumber($orderData['orderNumber'] ?? '');
        $orderImport->setOrderTotal($orderData['amountTotal'] ?? 0.0);
        $this->em->persist($orderImport);
        $this->em->flush();
        return $orderImport;
    }
}
