<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ShopwareOrderService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class HookController extends AbstractController
{
    #[Route('/hook/order', name: 'hook_order', methods: ['POST'])]
    public function order(Request $request, ShopwareOrderService $orderService): Response
    {
        $orderId = $request->get('orderId');
        if (!$orderId) {
            return new Response('Order ID fehlt', 400);
        }
        $result = $orderService->importOrder($orderId);
        if ($result === null) {
            return new Response('Order konnte nicht importiert werden', 500);
        }
        return new Response('Order importiert: ' . $orderId);
    }
}
